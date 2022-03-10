<?php

namespace Core\Database\Connection\MySQL\Event;

use Core\Connection\Mysql;
use Core\Environment;
use Core\Kernel;
use Core\Database;
use Core\Database\Connection\MySQL\Reader\Model;

class EventListener
{
    /**
     * @event core/kernel.boot
     */
    static function listenKernelBoot() {
        if (Environment::getConfiguration("DATABASE_ENABLE") === "true" && Environment::getConfiguration("DATABASE_DRIVER") === "mysql") {
            $connection = new Mysql([
                "host" => Environment::getConfiguration("MYSQL_HOST"),
                "port" => Environment::getConfiguration("MYSQL_PORT"),
                "name" => Environment::getConfiguration("MYSQL_NAME"),
                "user" => Environment::getConfiguration("MYSQL_USER"),
                "pass" => Environment::getConfiguration("MYSQL_PASS")
            ]);
            Database\Manager::setConnection($connection);
        }
    }

    /**
     * @param Database\Connection $connection
     * @event core/connection.set
     */
    static function listenConnectionSet(&$connection) {
        $modelReader = new Model();
        $connection->setModelReader($modelReader);
        foreach (Database\Manager::getScheme() as $tablename => $schemes) {
            if (isset($schemes["sql"]))
                Database\Manager::getConnection("mysql")->exec($schemes["sql"]);
        }
    }
}