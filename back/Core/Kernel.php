<?php

namespace Core;

class Kernel
{
    /**
     * @var \Core\Annotation\Annotation $annotation
     */
    static $annotation;

    /**
     * @var \Core\Routing\Routing $routing
     */
    static $routing;

    /**
     * @var \Core\Controller\Controller $controller
     */
    static $controller;

    /**
     * @var array
     */
    static $injected = [];

    /**
     * @var string $context
     */
    static $context;

    /**
     * Start
     */
    static function boot() {
        Response::initialize();
        self::$annotation = new Annotation();
        self::$routing = new Routing();
        self::$context = Environment::getConfiguration("APPLICATION_CONTEXT");
        Event::linkEvent();
        Event::exec("core/kernel.boot", $injection);
        self::inject($injection);
        $result = (self::$routing->getCurrent()["status"] === 200) ? self::makeControllerCall(self::$routing->getCurrent()) : null;
        if ($result !== null) {
            Response::setHeader(["Content-Type" => "application/json"]);
            Response::send();
            echo json_encode($result);
        } else
            Response::send();
    }

    /**
     * Injection from the core/kernel.boot event
     * @param $injection
     */
    static function inject($injection) {
        if (is_array($injection))
            foreach ($injection as $property => $value) {
                self::$injected[$property] = $value;
            }
    }

    /**
     * Return a service
     * @param $service
     * @return bool|mixed
     */
    static function get($service) {
        if (isset(self::$injected[$service]))
            return self::$injected[$service];
        else
            if (isset(self::$$service))
                return self::$$service;
            else
                return false;
    }

    /**
     * @param $current
     * @return mixed
     */
    static function makeControllerCall($current) {
        Event::exec("core/controller.preCall", $current);
        $controller = explode("->", $current["route"]["controller"]);
        if (!class_exists($controller[0]) || !method_exists((self::$controller = new $controller[0]()), $controller[1])) {
            Response::setStatus(500);
            Logger::log("kernel", "Controller class not found '" . $controller[0] . "'", Logger::$ERROR_LEVEL);
            return null;
        } else {
            $result = (in_array(\Core\Controller\Controller::class, class_implements(self::$controller))) ? self::$controller->{$controller[1]}($current) : false;
            Event::exec("core/controller.postCall", $result);
            return $result;
        }

    }

    /**
     * @return \Core\Annotation\Annotation $annotation
     */
    static function getAnnotation() {
        return self::$annotation;
    }
}