<?php

namespace Core;

use Core\Environment\Environment as Base;

class Environment implements Base
{
    static $configuration = [];

    /**
     * This function read a .env file and set configuration
     * @param $path
     * @return bool
     */
    static function read($path)
    {
        if (!defined("EXECUTION_BEGIN"))
            define("EXECUTION_BEGIN", self::getMicrotime());
        if (!file_exists($path))
            return false;
        $content = Files::read($path);
        $vars = explode("\n", $content);
        /**
         * Foreach setting in .env
         * Array : [Key, Value]
         */
        foreach ($vars as $var) {
            if (substr($var, 0, 1) !== "#"
                && count($configuration = explode("=", $var)) === 2) {
                self::set($configuration[0], $configuration[1]);
            }
        }
        return true;
    }

    /**
     * This function set a configuration pear key/value
     * @param $key
     * @param $value
     * @param $addToArray = false
     */
    static function set($key, $value, $addToArray = false) {
        if ($addToArray) {
            $keys = explode(".", $key);
            if (count($keys) > 1)
                self::$configuration[strtoupper($keys[0])][$key[1]] = trim($value);
            else
                self::$configuration[strtoupper($keys[0])][] = trim($value);
        }
        else
            self::$configuration[strtoupper($key)] = trim($value);
    }

    static function addExecutionTime($key) {
        self::$configuration["TIME"][$key] = self::getExecutionTime();
    }

    /**
     * This method return a configuration value or the configuration array
     * @param string $key
     * @return array|mixed|null
     */
    static function getConfiguration($key = "") {
        if ($key === "")
            return self::$configuration;
        else {
            if (array_key_exists($key, self::$configuration))
                return self::$configuration[$key];
            else
                return null;
        }
    }

    /**
     * This function return the current milisecond
     * @return int
     */
    static function getMicrotime() {
        return (int)(microtime(true) * 1000);
    }

    /**
     * Return current execution time
     * @return int
     */
    static function getExecutionTime() {
        return self::getMicrotime() - EXECUTION_BEGIN;
    }
}