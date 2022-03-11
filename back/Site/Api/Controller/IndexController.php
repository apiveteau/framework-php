<?php

namespace Api;

use Core\Controller;

class IndexController extends Controller {
    /**
     * @route /
     * @method GET
     */
    public function index() {
        $this->render("Site/Api/Templates/index", []);
    }
    /**
     * @route /test
     * @method GET
     * @return array
     */
    public function get()
    {
        return ["status" => 200, "data" => 'GET : Hello World'];
    }
    /**
     * @route /test
     * @method PUT
     * @return array
     */
    public function patch() {
        return ["status" => 200, "data" => 'PUT : Hello World'];
    }
    /**
     * @route /test
     * @method DELETE
     * @return array
     */
    public function delete() {
        return ["status" => 200, "data" => 'DELETE : Hello World'];
    }
    /**
     * @route /test
     * @method POST
     * @return array
     */
    public function post() {
        return ["status" => 200, "data" => 'POST : Hello World'];
    }
}