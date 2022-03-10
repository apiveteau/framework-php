<?php


namespace Core\Database;

use Core\Connection\Mysql;
use Core\Database\Model\Model as Base;
use Core\Event;

class Model implements Base
{
    /**
     * ID of the row in database
     * Primary key of table
     * basic configuration
     * @var int $id
     * @type integer
     * @primary true
     * @ai true
     */
    public $id = null;

    /**
     * Sorting
     * @var int $sorting
     * @type integer
     * @size 11
     * @default 0
     * @nullable false
     */
    public $sorting;

    /**
     * Date of creation
     * @var int $createdate
     * @type integer
     * @size 11
     * @default {time.current}
     * @nullable false
     */
    public $createdat;

    /**
     * Time of update
     * @var $updatedade
     * @type integer
     * @size 11
     * @default {time.current}
     * @nullable false
     */
    public $updatedat;

    /**
     * Model constructor.
     * @param $element
     * @param $sorting
     */
    public function __construct($element = [], $sorting = 0)
    {
        $this->createdat = time();
        $this->sorting = &$sorting;
        foreach ($element as $property => $value) {
            if (!is_numeric($property))
                $this->{$property} = $value;
        }
    }

    /**
     * Hash a password
     * @param &$value
     */
    public function hash(&$value) {
        $value = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * @return $this
     */
    public function save() {
        Event::exec("core/connection.modelSave", $this);
        $this->updatedat = time();
        if ($this->id === null) {
            unset($this->id);
            Manager::getConnection("mysql")->getQueryBuilder(get_class($this))->insert(get_class($this))->values(get_object_vars($this))->execute();
        } else {
            $id = $this->id;
            unset($this->createdat, $this->id);
            Manager::getConnection("mysql")->getQueryBuilder(get_class($this))->update(get_class($this))->fields(get_object_vars($this))->where([["id", "=", $id]])->execute();
        }
        return $this;
    }
}