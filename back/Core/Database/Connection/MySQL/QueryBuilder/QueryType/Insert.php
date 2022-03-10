<?php


namespace Core\Database\Connection\Mysql\Type;

/**
 * Class Insert
 * @package Core\Database\Connection\Mysql\Type
 */
class Insert extends BaseType
{
    /**
     * Delete constructor.
     * @param $tablename
     */
    public function __construct($tablename)
    {
        parent::$configuration = [];
        parent::__construct($tablename);
        parent::$configuration["table"]["sql"] = "INSERT INTO " . self::$configuration["table"]["name"];
    }

    /**
     * @param array $values
     * @return $this
     */
    public function values(array $values) {
        parent::$configuration["values"] = $values;
        $fieldString = " (";
        $valueString = " VALUES (";
        $i = 0;
        foreach ($values as $column => $value) {
            $fieldString .= "`" . strtolower($column) . "`";
            $valueString .= $this->quote($value);
            if ($i !== count($values) -1) {
                $fieldString .= ",";
                $valueString .= ",";
            }
            $i++;
        }
        $fieldString .= ")";
        $valueString .= ")";
        parent::$configuration["values"]["sql"] = $fieldString . $valueString;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getQuery()
    {
        return (!isset(parent::$configuration["values"]["sql"])) ?
            false : parent::$configuration["table"]["sql"] . parent::$configuration["values"]["sql"];
    }
}