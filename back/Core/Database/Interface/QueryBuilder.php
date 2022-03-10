<?php


namespace Core\Database;

/**
 * Interface QueryBuilder
 * @package Core\Database
 */
interface QueryBuilder
{
    /**
     * @param $fields
     * @return mixed
     */
    public function select($fields);
    /**
     * @param $tablename
     * @return mixed
     */
    public function update($tablename);
    /**
     * @param $tablename
     * @return mixed
     */
    public function delete($tablename);
    /**
     * @param $tablename
     * @return mixed
     */
    public function insert($tablename);
}