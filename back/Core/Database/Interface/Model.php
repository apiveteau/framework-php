<?php


namespace Core\Database\Model;


interface Model
{
    /**
     * @return Model
     */
    public function save();

    /**
     * @param string &$valueToHash
     */
    public function hash(&$valueToHash);
}