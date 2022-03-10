<?php


namespace Front\Controller;

use Core\Controller;
use Core\Response;
use Core\Security\Session;
use Front\Repository\UserRepository;

class DefaultController extends Controller
{
    /**
     * Home Controller
     */
    public function index() {
        $this->render("Site/Front/Resource/html/home", []);
    }

    public function renderPage() {
        $this->render("Site/Front/Resource/html/default", []);
    }

    /**
     * Icons page sample
     */
    public function icons() {
        $this->render("Site/Front/Resource/html/icons", []);
    }
    /**
     * 404 Not found
     */
    public function notFound() {
        $this->render("Site/Front/Resource/html/404", []);
    }
}