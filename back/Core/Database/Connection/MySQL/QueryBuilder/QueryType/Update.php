<?php


namespace Core\Database\Connection\Mysql\Type;

/**
 * Class Update
 * @package Core\Database\Connection\Mysql\Type
 */
class Update extends BaseType
{
    /**
     * Update constructor.
     * @param $tablename
     */
    public function __construct($tablename)
    {
        parent::$configuration = [];
        parent::__construct($tablename);
        parent::$configuration["table"]["sql"] = "UPDATE " . self::$configuration["table"]["name"] . " SET ";
    }

    /**
     * @param array $fieldsAndValues
     * @return $this
     */
    public function fields(array $fieldsAndValues) {
        parent::$configuration["update"] = $fieldsAndValues;
        parent::$configuration["update"]["sql"] = "";
        $i = 0;
        foreach ($fieldsAndValues as $field => $value) {
            parent::$configuration["update"]["sql"] .= " `" . strtolower($field) . "` = " . $this->quote($value) . " ";
            if ($i !== count($fieldsAndValues) - 1)
                parent::$configuration["update"]["sql"] .= ", ";
            $i++;
        }
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getQuery()
    {
        return (!isset(parent::$configuration["where"]["sql"]) || !isset(parent::$configuration["update"]["sql"])) ?
            false : parent::$configuration["table"]["sql"] . parent::$configuration["update"]["sql"] . parent::$configuration["where"]["sql"];
    }
}