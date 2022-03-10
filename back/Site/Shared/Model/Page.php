<?php


namespace Front\Model;


use Core\Database\Model;

class Page extends Model
{
    /**
     * @type varchar
     * @size 512
     * @var $title
     */
    public $title;
    /**
     * @type text
     * @var $description
     */
    public $description;
    /**
     * @type text
     * @var $image
     */
    public $image;
    /**
     * @type text
     * @var $content
     */
    public $content;
    /**
     * @type text
     * @var $keywords
     */
    public $keywords;
    /**
     * @type varchar
     * @size 512
     * @unique true
     * @var $slug
     */
    public $slug;
    /**
     * @type integer
     * @size 11
     * @var $parent
     */
    public $parent;
    /**
     * @type integer
     * @size 11
     * @foreign Front\Model\User
     * @var $auth
     */
    public $auth;
}