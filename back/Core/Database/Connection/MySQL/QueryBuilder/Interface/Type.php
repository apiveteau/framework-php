<?php


namespace Core\Database\Connection\MySQL\Type;

/**
 * Interface Type
 * @package Core\Database\Connection\MySQL\Type
 */
interface Type
{
    /**
     * @return string
     */
    public function getQuery();

    /**
     * @return array
     */
    public function getConfiguration();
}