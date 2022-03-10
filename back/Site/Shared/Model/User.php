<?php


namespace Front\Model;


use Core\Database\Model;

class User extends Model
{
    /**
     * @type varchar
     * @size 255
     * @var $title
     */
    public $username;
    /**
     * @type varchar
     * @size 1024
     * @var $password
     */
    public $password;
    /**
     * @type varchar
     * @size 1024
     * @var $email
     */
    public $email;
    /**
     * @type varchar
     * @size 255
     * @var $fullname
     */
    public $fullname;
    /**
     * @type varchar
     * @size 1024
     * @var $image
     */
    public $image;
    /**
     * @type text
     * @var $description
     */
    public $description;
    /**
     * @type varchar
     * @size 1024
     * @var $level
     */
    public $level;
}