<?php

namespace Core\Database\Connection\MySQL\Reader;

use Core\Database\Manager;
use Core\Files;
use Core\Kernel;

class Model
{
    private $modelsDocumentation = [];
    private $schema = [];

    /**
     * Model constructor.
     */
    public function __construct() {
        $this->extractModel();
        if (empty($this->modelsDocumentation))
            return;
        $this->makeConfigurationArray();
        $this->interpret();
        $this->save();
    }

    /**
     * Extract model class from all explored classes
     */
    private function extractModel() {

        foreach (($documentation = Kernel::getAnnotation()->getDocumentation())["classes"] as $workspace => &$classes) {
            if ($workspace !== "Core")
                foreach ($classes as $class) {
                    if (strpos($class, "Model") !== false)
                        $this->modelsDocumentation[$workspace . "\\" . $class] = $documentation[$workspace . "\\" . $class];
                }
        }
    }

    /**
     * This function build schema from model class found
     */
    private function makeConfigurationArray() {
        foreach ($this->modelsDocumentation as $classname => $documentation) {
            $table = Manager::getConnection("mysql")->getTableName($classname);
            $properties = $documentation["properties"];
            foreach ($properties as $index => &$configuration) {
                $column = strtolower($index);
                foreach ($configuration as $config => &$value) {
                    if (in_array($config, ["type", "size", "default", "nullable", "primary", "ai", "unique"]))
                        $this->schema["local"][$table][$column][$config] = trim($value[0]);
                    if (in_array($config, ["foreign", "refer"])) {
                        $this->schema["local"][$table][$column][$config] = $this->extractForeignConfiguration(trim($value[0]));
                    }
                }
            }
        }
    }

    /**
     * This function call the 2 way to interpret the configuration array of models
     */
    private function interpret() {
        $this->generateJSON();
        $this->generateSQL();
    }

    /**
     * This function save schema if it is different than existing or new
     */
    private function save() {
        foreach ($this->schema["json"] as $filename => $content) {
            Manager::addScheme($filename, ["json" => $content, "sql" => $this->schema["sql"][$filename]]);
            $filepathSchema = "Cache/database/schema/" . $filename . ".json";
            $filepathSql = "Cache/database/sql/" . $filename . ".sql";
            if (!file_exists($filepathSchema) || filesize($filepathSchema) !== strlen($content))
                Files::put($filepathSchema, $content, true);
            if (!file_exists($filepathSql) || filesize($filepathSql) !== strlen($this->schema["sql"][$filename]))
                Files::put($filepathSql, $this->schema["sql"][$filename], true);
        }
    }


    /**
     * This function build the json part of schema array, to save to file
     */
    private function generateJSON() {
        foreach ($this->schema["local"] as $table => $schemaConfiguration) {
            $this->schema["json"][$table] = json_encode($schemaConfiguration);
        }
    }

    /**
     * This function will make all sql file for table
     */
    private function generateSQL() {
        foreach ($this->schema["local"] as $table => $schemaConfiguration) {
            $this->schema["sql"][$table] = "CREATE TABLE `" . $table . "` (";
            $index = 0;
            $lines = "";
            $isLast = false;
            $foreign = [];
            foreach ($schemaConfiguration as $column => $configuration) {
                if ($index === count($schemaConfiguration) - 1)
                    $isLast = true;
                if (isset($configuration["foreign"]))
                    $foreign[$column] = $configuration["foreign"];
                $lines .= $this->makeSqlLine($column, $configuration, $isLast);
                $index++;
            }
            $index = 0;
            foreach ($foreign as $column => $configuration) {
                if (isset($configuration["class"])) {
                    $lines .= ", FOREIGN KEY (" . $column . ") REFERENCES " . Manager::getConnection("mysql")->getTableName($configuration["class"]) . "(id)";
                }
                $index++;
            }
            $this->schema["sql"][$table] .= $lines . ");";
        }
    }

    /**
     * This function return the foreign configuration array
     * @param $configurationString
     * @return array
     */
    private function extractForeignConfiguration($configurationString) {
        $returnable = [];
        $values = explode(" ", $configurationString);
        foreach ($values as $value) {
            $association = explode(":", $value);
            if (count($association) === 2)
                $returnable[$association[0]] = $association[1];
            else if (count($association) === 1)
                $returnable["class"] = $association[0];
        }
        return $returnable;
    }

    /**
     * This function read a column configuration and convert it to a SQL column line creation
     * @param $column
     * @param $configuration
     * @param bool $isLast
     * @return bool|string
     */
    private function makeSqlLine($column, $configuration, $isLast = false) {
        if (!isset($configuration["type"]))
            return false;
        $sql = "`" . $column . "` " . strtoupper($configuration["type"]);
        if (isset($configuration["size"]))
            $sql .= "(" . $configuration["size"] . ")";
        if (isset($configuration["primary"]) && $configuration["primary"] === "true") {
            $sql .= " PRIMARY KEY";
        }
        if (isset($configuration["default"]))
            $sql .= " DEFAULT " . $this->format($configuration["default"]);
        if (isset($configuration["nullable"]))
            $sql .= ($configuration["nullable"] === "true") ? " NULL" : " NOT NULL";
        if (isset($configuration["unique"]) && $configuration["unique"] === "true")
            $sql .= " UNIQUE";
        if (isset($configuration["primary"]) && $configuration["primary"] === "true")
            $sql .= " AUTO_INCREMENT";
        if (!$isLast)
            $sql .= ",";
        return $sql;
    }

    /**
     * This function interpret default values markers use
     * @param $value
     * @return mixed
     */
    private function format($value) {
        return str_replace("{time.current}", time(), $value);
    }
}