<?php


namespace Core\Files;


interface Files
{
    /**
     * This function return file contents
     * @param $path
     * @return false|mixed|string
     */
    static function read($path);

    /**
     * This function put content into $path given
     * @param $path
     * @param $content
     * @return bool|int|mixed
     */
    static function put($path, $content);

    /**
     * This function delete $path file
     * @param $path
     * @return mixed|void
     */
    static function delete($path);

    /**
     * This function create a $path file given
     * @param $content = ""
     * @param $path
     * @return mixed|void
     */
    static function create($path, $content = "");

    /**
     * This function test a $path given and create folder if not exist
     * @param $path
     * @return bool|mixed
     */
    static function test($path);
}