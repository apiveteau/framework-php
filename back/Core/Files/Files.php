<?php


namespace Core;

use Core\Files\Files as Base;

class Files implements Base
{
    /**
     * This function return file contents
     * @param $path
     * @return false|string
     */
    static function read($path) {
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        else {
            Logger::log("files", "Cannot read from '" . $path . "' file");
            return false;
        }
    }

    /**
     * This function put content into $path given
     * @param $path
     * @param $content
     * @param $deleteIfExist = false
     * @return bool|int|mixed
     */
    static function put($path, $content, $deleteIfExist = false) {
        if ($deleteIfExist)
            self::delete($path);
        self::test($path);
        if (!($result = file_put_contents($path, $content . PHP_EOL, FILE_APPEND | LOCK_EX)))
            Logger::log("files", "Cannot add content to file '" . $path . "'. You must check right and give R & W to Log, Cache and Public folders.", Logger::$ERROR_LEVEL);
        return $result;
    }

    /**
     * This function delete $path file
     * @param $path
     * @return mixed|void
     */
    static function delete($path) {
        if (file_exists($path))
            unlink($path);
        else
            Logger::log("files", "Try to delete a non-existing file '" . $path . "'", Logger::$WARNING_LEVEL);
    }

    /**
     * This function create a $path file given
     * @param $path
     * @param $content = ""
     * @return mixed|void
     */
    static function create($path, $content = "") {
        self::test($path);
        $fd = fopen($path, "a+");
        fclose($fd);
        if ($content !== "")
            self::put($path, $content);
    }

    /**
     * This function test a $path given and create folder if not exist
     * How it work, he get a path like /a/b/c and parse it like this :
     * /a
     * /a/b
     * /a/b/c
     * if a folder does not exist, create it
     * @param $path
     * @return bool|mixed
     */
    static function test($path) {
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        if (!file_exists($path)) {
            $elements = explode(DIRECTORY_SEPARATOR, $path);
            $accumulator = "";
            foreach ($elements as $element) {
                if ($accumulator === "")
                    $accumulator = $element;
                else
                    $accumulator .= $element;
                if (strpos($element, ".") === false && !file_exists($accumulator) && $accumulator !== "") {
                    mkdir($accumulator);
                }
                if (strpos($element, "."))
                    return file_exists($accumulator);
                $accumulator .= DIRECTORY_SEPARATOR;
            }
        }
        return true;
    }
}