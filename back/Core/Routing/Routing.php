<?php


namespace Core;

use Core\Routing\Routing as Base;

class Routing implements Base
{
    /**
     * Containing the current existing route information
     * @var array $current
     */
    private $current = [];
    /**
     * Containing all route as interpretable array
     * @var array $routes
     */
    private $routes = ["static" => ["GET" => [], "PUT" => [], "POST" => [], "DELETE" => []]];
    /**
     * Status code of return
     * @var int $status
     */
    public $status;

    public function __construct() {
        $this->status = $this->router()->setCurrent();
        Response::setStatus($this->status);
        Event::exec("core/routing.current", $this->current);
        $this->checkEmptySite();
        Environment::addExecutionTime("router");
    }

    /**
     * Read all different type of routes declared :
     * - Get .routing files
     * - Get @route annotation
     */
    private function router() {
        foreach (Loader::$ROUTING as $route)
            $this->read($route);
        $annotation = Kernel::getAnnotation()->getDocumentation();
        foreach ($annotation as $classname => $conf) {
            foreach ($conf as $method => $configuration) {
                if (isset($configuration) && array_key_exists("route", $configuration)) {
                    if (isset($configuration["site"])) {
                        foreach ($configuration["route"] as $route) {
                            $this->routes[$configuration["site"]][$route] = ["GET" => [], "PUT" => [], "POST" => [], "DELETE" => []];
                            $this->routes[trim($configuration["site"][0])][$configuration["method"] ?? 'GET'][trim($route)] = $classname . "->" . $method;
                        }
                    } else {
                        foreach ($configuration["route"] as $route) {
                            $this->routes["static"][trim($route)][$configuration["method"][0] ?? 'GET'] = $classname . "->" . $method;
                        }
                    }
                }
            }
        }
        Event::exec("core/routing.read", $this->routes);
        Logger::log("core/routing.read", json_encode($this->routes), Logger::$DEBUG_LEVEL);
        return $this;
    }

    /**
     * Tell if a site of app doesn't had existing route
     */
    private function checkEmptySite() {
        foreach (explode(",", Environment::getConfiguration("SITES_DOMAINS")) as $site) {
            if ((!isset($this->routes[$site]) || empty($this->routes[$site])) && !isset($this->routes["static"])) {
                Logger::log("routing", "Site '" . $site . "' has no route", Logger::$WARNING_LEVEL);
            }
        }
    }

    /**
     * This function read a .routing json file and create associate route into memory
     * @param $path
     */
    private function read($path) {
        $routes = json_decode(Files::read($path));
        foreach ($routes as $site => $configuration) {
            foreach($configuration as $method => $routes) {
                foreach ($routes as $route => $controller) {
                    $this->routes[$site][$route][$method] = $controller;
                }
            }
        }
    }

    /**
     * This function define which route of which site is used, it return the http response status
     * @return int
     */
    private function setCurrent() {
        $site = $_SERVER["HTTP_HOST"];
        $uri = $_SERVER["REQUEST_URI"];
        $method = $_SERVER["REQUEST_METHOD"];
        $status = 200;
        if (!isset($this->routes[$site]) && !isset($this->routes['static'])) {
            $status = 500;
        } else {
            if (isset($this->routes[$site][$uri]) && isset($this->routes[$site][$uri][$method])) {
                $this->define($uri, $this->routes[$site][$uri], $site);
            } elseif (isset($this->routes["static"][$uri]) && $this->routes["static"][$uri][$method]) {
                $this->define($uri, $this->routes["static"][$uri][$method], $site);
            } else {
                if (!$this->checkRouteParams($site, $uri, $method) && !$this->checkRouteParams("static", $uri, $method)) {
                    $status = 404;
                }
            }
        }
        Logger::log("routing", "" . $_SERVER["REQUEST_URI"] . ":" . $status, Logger::$DEFAULT_LEVEL);
        Event::exec("core/routing." . $status, $this->routes);
        return $status;
    }

    /**
     * This function define the final current route use
     * @param $route
     * @param $controller
     * @param $site
     * @param array $arguments
     * @return $this
     */
    public function define($route, $controller, $site, $arguments = []) {
        $this->current["route"] = $route;
        $this->current["controller"] = $controller;
        $this->current["site"] = $site;
        $this->current["arguments"] = $arguments;
        $this->current["arguments"]["post"] = &$_POST;
        Logger::log("routing", "Current '" . $route . "' -> " . $controller . " [" . $site . "](" . count($arguments) . ")", Logger::$DEFAULT_LEVEL);
        return $this;
    }

    /**
     * This function tell if a subelement of URI is a param of route
     * @param $SubURIElement
     * @return bool
     */
    private function isURIParameter($SubURIElement) {
        return (substr($SubURIElement, 0, 1) === "{" && substr($SubURIElement, strlen($SubURIElement) - 1, 1) === "}");
    }

    /**
     * This function compare existing route to the server request route, it can define the current Route
     * @param $site
     * @param $uri
     * @return bool
     */
    private function checkRouteParams($site, $uri, $method) {
        $data = $this->routes[$site] ?? $this->routes["static"];
        foreach ($data as $route => $methods) {
            foreach ($methods as $route_method => $controller) {
                $existingRouteArray = array_values(array_filter(explode("/", $route)));
                $testingRouteArray = array_values(array_filter(explode("/", $uri)));
                $index = 0;
                $tempParameter = [];
                if (count($testingRouteArray) === count($existingRouteArray)) {
                    while ($index < count($testingRouteArray)) {
                        if (($testingRouteArray[$index] === $existingRouteArray[$index]) || ($testingRouteArray[$index] !== $existingRouteArray[$index] && $this->isURIParameter($existingRouteArray[$index]))) {
                            if ($this->isURIParameter($existingRouteArray[$index]))
                                $tempParameter[substr($existingRouteArray[$index], 1, strlen($existingRouteArray[$index]) - 2)] = $testingRouteArray[$index];
                            $index++;
                            if ($index === count($testingRouteArray) && $route_method === $method) {
                                $this->define($uri, $controller, $site, $tempParameter);
                                return true;
                            }
                        } else
                            $index = count($testingRouteArray);
                    }
                }
            }
        }
        return false;
    }

    /**
     * This function return the current route and the status code
     * @return array
     */
    public function getCurrent() {
        return ["route" => $this->current, "status" => $this->status];
    }
}