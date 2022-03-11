<?php

namespace Api;

use Core\Controller;

class IndexController extends Controller {
    /**
     * @route /
     * @method GET
     * @return array
     */
    public function index() {
        return ["status" => 200, "data" => 'GET : Hello World'];
    }
    /**
     * @route /
     * @method PUT
     * @return array
     */
    public function patch() {
        return ["status" => 200, "data" => 'PUT : Hello World'];
    }
    /**
     * @route /
     * @method DELETE
     * @return array
     */
    public function delete() {
        return ["status" => 200, "data" => 'DELETE : Hello World'];
    }
    /**
     * @route /
     * @method POST
     * @return array
     */
    public function post() {
        return ["status" => 200, "data" => 'POST : Hello World'];
    }
}