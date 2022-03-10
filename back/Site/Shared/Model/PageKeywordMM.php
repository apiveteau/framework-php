<?php


namespace Front\Model;


use Core\Database\Model;

class PageKeywordMM extends Model
{
    /**
     * @type integer
     * @size 11
     * @foreign Front\Model\PageKeywordMM
     * @size 1024
     * @var $page
     */
    public $page;
    /**
     * @type integer
     * @size 11
     * @foreign Front\Model\PageKeywordMM
     * @size 1024
     * @var $keyword
     */
    public $keyword;
}