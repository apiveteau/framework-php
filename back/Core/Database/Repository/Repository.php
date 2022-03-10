<?php


namespace Core\Database;


class Repository
{
    protected $classname = "";

    public function __construct($classname = "")
    {
        if ($classname !== "") {
            $this->classname = $classname;
        }
    }

    public function setClass($classname) {
        $this->classname = $classname;
        return $this;
    }

    public function findAll() {
        return ($this->classname !== "") ? Manager::getConnection("mysql")
            ->getQueryBuilder($this->classname)
            ->select("*")
            ->from($this->classname)
            ->execute() : false;
    }

    public function findBy($column, $value, $operator = "=")
    {
        return ($this->classname !== "") ? Manager::getConnection("mysql")
            ->getQueryBuilder($this->classname)
            ->select("*")
            ->from($this->classname)
            ->where([
                [
                    $column,
                    $operator,
                    $value
                ]
            ])
            ->execute() : false;
    }
}