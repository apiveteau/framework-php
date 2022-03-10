<?php


namespace Core\Database\Connection\Mysql\Type;

/**
 * Class Delete
 * @package Core\Database\Connection\Mysql\Type
 */
class Delete extends BaseType
{
    /**
     * Delete constructor.
     * @param $tablename
     */
    public function __construct($tablename)
    {
        parent::$configuration = [];
        parent::__construct($tablename);
        parent::$configuration["table"]["sql"] = "DELETE FROM " . self::$configuration["table"]["name"];
    }

    /**
     * @return bool|string
     */
    public function getQuery()
    {
        return (!isset(parent::$configuration["where"]["sql"])) ?
            false : parent::$configuration["table"]["sql"] . parent::$configuration["where"]["sql"];
    }
}