<?php


namespace Core\Connection;

use Core\Connection\Mysql\QueryBuilder;
use Core\Database\Connection;
use Core\Database\Model\Model;
use Core\Environment;
use Core\Files;
use Core\Kernel;
use Core\Logger;

/**
 * Class Mysql
 * @package Core\Connection
 */
class Mysql implements Connection
{
    /**
     * @var string
     */
    private $name = "mysql";
    /**
     * @var null|mixed
     */
    private $modelReader = null;
    /**
     * @var \PDO|null
     */
    private $pdo = null;
    /**
     * @param $identity
     */
    public function __construct($identity) {
        try {
            $this->pdo = new \PDO(
                'mysql:host=' . $identity["host"] . ':' . $identity["port"] .  ';dbname=' . $identity["name"],
                $identity["user"],
                $identity["pass"],
                [
                    \PDO::ATTR_PERSISTENT => true
                ]
            );
        } catch (\PDOException $exception) {
            Logger::log("database", "Connection error: " . $exception->getMessage(), Logger::$ERROR_LEVEL);
            //TODO: Create an exception thrower
        }
    }
    /**
     * @param string $query
     * @return false|mixed|\PDOStatement
     */
    public function exec($query = "") {
        if (Environment::getConfiguration("LOG_QUERY") === "true")
            Files::put("Log/database/history.log", $query);
        return $this->pdo->query($query);
    }
    /**
     * @param string $tablename
     * @return QueryBuilder|mixed
     */
    public function getQueryBuilder($tablename = "") {
        return new QueryBuilder($tablename);
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param $reader
     * @return mixed|void
     */
    public function setModelReader(&$reader)
    {
        $this->modelReader = $reader;
    }
    /**
     * @param $string
     * @return string
     */
    public function getTableName($string) {
        if (strpos($string, "\\") !== false) {
            $value = strtolower(str_replace("\\", "_", $string));
        } else {
            $index = 0;
            $string = explode("_", $string);
            $classname = "";
            while ($index < count($string)) {
                $classname .= ucfirst($string[$index]);
                if ($index !== count($string) - 1)
                    $classname .= "\\";
                $index++;
            }
            $value = $classname;
        }
        return $value;
    }

    /**
     * @param $elements
     * @param array $result
     * @return array<Model>
     */
    public function convert(&$elements, $result = []) {
        if ($elements === false || !is_array($elements))
            return [];
        $table = $this->getTableName(array_keys($elements)[0]);
        $elements = $elements[array_keys($elements)[0]];
        foreach ($elements as $sorting => $element) {
            $result[] = new $table($element, $sorting);
        }
        return $result;
    }
}