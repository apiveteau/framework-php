<?php


namespace Core\Database\Connection\Mysql\Type;

use Core\Connection\Mysql;
use Core\Database\Manager;

/**
 * Class Select
 * @package Core\Database\Connection\Mysql\Type
 */
class Select extends BaseType
{
    /**
     * Select constructor.
     * @param array $fields
     */
    public function __construct($fields, $tablename) {
        parent::$configuration = [];
        if ($fields !== "*") {
            parent::$configuration["fields"] = $fields;
            foreach ($fields as $field) {
                parent::$configuration["fields"][] = "`" . $field . "`";
            }
            parent::$configuration["fields"]["sql"] = "SELECT " . implode(",", parent::$configuration["fields"]);
        } else {
            parent::$configuration["fields"][] = "*";
            parent::$configuration["fields"]["sql"] = "SELECT * ";
        }
        parent::__construct($tablename);
    }

    /**
     * @param string|array $tablename
     * @return $this
     */
    public function from($tablename)
    {
        if (is_array($tablename)) {
            parent::$configuration["from"] = $tablename;
            parent::$configuration["from"]["sql"] = " FROM " . Manager::getConnection("mysql")->getTableName(parent::$configuration["from"]);
        } else {
            parent::$configuration["from"][] = $tablename;
            parent::$configuration["from"]["sql"] = " FROM " . Manager::getConnection("mysql")->getTableName($tablename);
        }
        return $this;
    }

    public function execute()
    {
        $tablename = self::$configuration["table"]["name"];
        $pdoResult = Manager::getConnection("mysql")->exec($this->getQuery());
        if ($pdoResult !== false) {
            $models[$tablename] = $pdoResult->fetchAll();
            if (is_array($models[$tablename]))
               return Manager::getConnection("mysql")->convert($models);
        }
        return false;
    }

    /**
     * @return string $sql
     */
    public function getQuery() {
        $query = "";
        if (!isset(parent::$configuration["fields"]["sql"]) || !isset(parent::$configuration["from"]["sql"]))
            return false;
        $query .= parent::$configuration["fields"]["sql"] . parent::$configuration["from"]["sql"];
        if (isset(parent::$configuration["join"]["inner"]["sql"]))
            $query .= parent::$configuration["join"]["inner"]["sql"];
        if (isset(parent::$configuration["where"]["sql"]))
            $query .= parent::$configuration["where"]["sql"];
        if (isset(parent::$configuration["offset"]["sql"]))
            $query .= parent::$configuration["offset"]["sql"];
        if (isset(parent::$configuration["limit"]["sql"]))
            $query .= parent::$configuration["limit"]["sql"];
        return $query;
    }
}