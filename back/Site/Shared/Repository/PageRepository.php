<?php


namespace Front\Repository;


use Core\Database\Manager;
use Core\Database\Repository;
use Core\Logger;
use Front\Model\Page;
use Front\Model\User;

class PageRepository extends Repository
{
    /**
     * @var array Page
     */
    private $pages;
    /**
     * @var Page $current
     */
    private $current;
    /**
     * @var $menu
     */
    private $menu;

    public function __construct($classname = "")
    {
        $classname = Page::class;
        parent::__construct($classname);
        $this->pages = Manager::getConnection("mysql")
            ->getQueryBuilder(Page::class)
            ->select("*")
            ->from(Page::class)
            ->execute();
        foreach ($this->pages as $page) {
            $page->readableContent = htmlentities($page->content);
            if ($page->slug === $_SERVER["REQUEST_URI"]) {
                $this->current = $page;
            }
            $this->getPageInformation($page);
        }
        $this->menu = $this->makeMenu();
    }

    /**
     * Override default findBy method to increase performance
     * @param $column
     * @param $value
     * @param string $operator
     * @return array|bool|mixed
     */
    public function findBy($column, $value, $operator = "=")
    {
        foreach ($this->pages as $page) {
            if ($page->{$column} === $value)
                return $page;
        }
        return [];
    }
    /**
     * Return all pages
     * @return array
     */
    public function getPages() {
        return $this->pages;
    }

    /**
     * Return current page
     * @return Page|mixed
     */
    public function getCurrentPage() {
        return $this->current;
    }

    /**
     * @return array
     */
    public function getMenu() {
        return $this->menu;
    }

    /**
     * @return array
     */
    private function makeMenu() {
        $menu = [];
        foreach ($this->pages as &$page) {
            if ((int)$page->parent === 0) {
                $menu[] = $page;
            }
        }
        return $menu;
    }

    /**
     * Get page child
     * @param $id
     * @return array
     */
    private function getPageChild($id) {
        $child = [];
        foreach ($this->pages as &$page) {
            if ($page->parent === $id)
                $child[] = $page;
        }
        return $child;
    }

    /**
     * Get page informations
     * @param $page
     */
    private function getPageInformation(&$page) {
        $page->auth = Manager::getConnection("mysql")
            ->getQueryBuilder(User::class)
            ->select("*")
            ->from(User::class)
            ->where([["id", "=", $page->auth]])
            ->execute()[0];
        $page->child = $this->getPageChild($page->id);
    }
}