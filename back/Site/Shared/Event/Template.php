<?php


namespace Front\Event;

use Core\Environment;
use Core\Security\Session;
use Front\Repository\PageRepository;

class Template
{
    /**
     * Add arguments to templates
     * @event core/template.preProcess
     * @param $args
     */
    static function preProcess(&$args) {
        $currentPage = ($pageRepository = new PageRepository())->getCurrentPage();
        $args["title"] = Environment::getConfiguration("APPLICATION_NAME");
        $args["time"] = time();
        if (is_object($currentPage)) {
            $args["page"] = \Core\Template::object_to_array($currentPage);
        }
        if (Session::get()) {
            $args["user"] = $_SESSION;
        }
        $args["menu"] = \Core\Template::object_to_array($pageRepository->getMenu());
    }
}