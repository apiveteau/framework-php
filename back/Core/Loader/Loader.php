<?php

namespace Core;

include_once __DIR__ . "/Interface/LoaderBase.php";

use \Core\Loader\LoaderBase;

class Loader implements LoaderBase
{
    static $CLASSES = [];
    static $ROUTING = [];

    /**
     * This function load classes by path with constraint or needle
     * @param $path
     * @param string $needle
     * @param string $exclude
     * @param int $depth
     * @return mixed|void
     */
    static function explore($path, $needle = "", $exclude = "", $depth = 0)
    {
        if (file_exists($path . ".routing"))
            self::$ROUTING[] = $path . ".routing";
        if (file_exists($path . DIRECTORY_SEPARATOR . ".routing"))
            self::$ROUTING[] = $path . DIRECTORY_SEPARATOR . ".routing";
        $scan = glob($path . DIRECTORY_SEPARATOR . "*");
        foreach ($scan as $path) {
            if (is_dir($path)) {
                self::explore($path, $needle, $exclude, $depth + 1);
            } else {
                if (!in_array($path, self::$CLASSES)) {
                    if (strpos($path, ".php") !== false) {
                        if (($needle === "" || strpos($path, $needle) !== false) && ($exclude === "" || strpos($path, $exclude) === false) && strpos($path, "Commander") === false) {
                            self::$CLASSES[] = $path;
                            require_once $path;
                        }
                    }
                }
            }
        }
    }

    /**
     * Return if a specified class is loaded
     * @param $classname
     * @return bool
     */
    static function isLoaded($classname) {
        return in_array(self::$CLASSES, $classname);
    }
}