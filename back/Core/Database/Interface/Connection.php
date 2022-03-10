<?php


namespace Core\Database;

/**
 * Interface Connection
 * @package Core\Database
 */
interface Connection
{
    /**
     * Return driver name
     * @return string
     */
    public function getName();

    /**
     * @param $reader
     * @return mixed
     */
    public function setModelReader(&$reader);

    /**
     * Return a QueryBuilder instance
     * @param string $table
     * @return mixed
     */
    public function getQueryBuilder($table = "");

    /**
     * Execute a query generate by QueryBuilder
     * @param string $query
     * @return mixed
     */
    public function exec($query = "");

    /**
     * @param $elements
     * @param array $result
     * @return array<\Core\Database\Model\Model>
     */
    public function convert(&$elements, $result = []);

    /**
     * @param string $classname
     * @return string $tablename
     */
    public function getTableName($classname);
}