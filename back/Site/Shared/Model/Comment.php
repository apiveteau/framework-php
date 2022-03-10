<?php


namespace Front\Model;


use Core\Database\Model;

class Comment extends Model
{
    /**
     * @type varchar
     * @size 512
     * @var $comment
     */
    public $comment;
    /**
     * @type integer
     * @size 11
     * @foreign Front\Model\User
     * @size 1024
     * @var $auth
     */
    public $auth;
}