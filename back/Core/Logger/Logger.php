<?php

namespace Core;

use Core\Logger\Logger as LoggerBase;

class Logger implements LoggerBase
{
    /**
     * @var int
     */
    static $DEBUG_LEVEL = 3;
    /**
     * Need by Logger to define in which folder put the file
     * @var int $DEFAULT_LEVEL
     */
    static $DEFAULT_LEVEL = 2;
    /**
     * Need by Logger to define in which folder put the file
     * @var int $WARNING_LEVEL
     */
    static $WARNING_LEVEL = 1;
    /**
     * Need by Logger to define in which folder put the file
     * @var int $ERROR_LEVEL
     */
    static $ERROR_LEVEL = 0;

    static $FOLDERS = ["error", "warning", "default", "debug"];

    /**
     * This function write in log file split by specific format
     * @param string $key is use as sub-folder of Log folder
     * @param string $message log content
     * @param int $status this value is needed for split log like warning/errors
     * @return bool
     */
    public static function log($key = "", $message = "", $status = 0)
    {
        if ($key === "" || $message === "" || $status >= (int)Environment::getConfiguration("LOG_LEVEL"))
            return false;
        Files::put(self::makeLogPath(), strtoupper(self::$FOLDERS[$status]) . "|" . strtoupper($key) . "|" . Environment::getMicrotime() . "|" . $message);
        return true;
    }


    /**
     * Return last log
     * @param $linesNbr
     * @return array
     */
    public static function tail($linesNbr = 5) {
        $logFromFile = Files::read(self::makeLogPath());
        $usableLogs = explode("\n", $logFromFile);
        $index = count($usableLogs) - 1;
        $result = [];
        while ($linesNbr > 0 && $index > 0) {
            $result[] = $usableLogs[$index - 1];
            $linesNbr--;
            $index--;
        }
        return $result;
    }

    /**
     * This function build the log file path
     * @param $key
     * @return string
     */
    private static function makeLogPath() {
        if (Environment::getConfiguration("LOG_SPLIT_BY_SITE") === "true")
            $directory =  PATH_LOG  . str_replace(".", "-", $_SERVER["HTTP_HOST"]) . DIRECTORY_SEPARATOR;
        else
            $directory =  PATH_LOG;
        Files::test($directory);
        $timeHash = date(Environment::getConfiguration("TEMP_FORMAT"), time());
        $fileExt = ".log";
        return $directory . $timeHash . $fileExt;
    }

    public static function dumpAndDie($var) {
        echo "<pre><code>" , var_dump($var), "</code></pre>"; die;
    }
}