<?php

namespace Core\Database;

use Core\Event;

class Manager
{
    /**
     * @var Connection
     */
    private static $connection = [];

    /**
     * @var array $schemes
     */
    private static $schemes = [];

    /**
     * @param string $driver
     * @return Connection|false
     */
    static function getConnection($driver) {
        return (isset(self::$connection[$driver])) ? self::$connection[$driver] : false;
    }

    /**
     * @param Connection &$connection
     */
    static function setConnection(&$connection) {
        self::$connection[$connection->getName()] = $connection;
        Event::exec("core/connection.set", $connection);
    }

    /**
     * Add a scheme to Database Manager
     * @param array $scheme
     * @param string $schemeName
     */
    static function addScheme($schemeName, array $scheme) {
        self::$schemes[$schemeName] = $scheme;
    }

    /**
     * @return array of schemes
     */
    static function getScheme() {
        return self::$schemes;
    }
}