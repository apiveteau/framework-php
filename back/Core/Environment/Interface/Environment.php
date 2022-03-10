<?php


namespace Core\Environment;


interface Environment
{
    /**
     * This method return a configuration value or the configuration array
     * @param string $key
     * @return array|mixed|null
     */
    static function getConfiguration($key = "");

    /**
     * Return current execution time
     * @return int
     */
    static function getExecutionTime();

    /**
     * This function return the current milisecond
     * @return int
     */
    static function getMicrotime();

    /**
     * This function set a configuration pear key/value
     * @param $key
     * @param $value
     * @param $addToArray = false
     */
    static function set($key, $value, $addToArray = false);
}