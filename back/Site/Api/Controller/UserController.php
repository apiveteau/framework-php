<?php


namespace Api\Controller;


use Core\Controller;
use Core\Database\Manager;
use Core\Security\Session;
use Front\Model\User;
use Front\Repository\UserRepository;

class UserController extends Controller
{
    public function __construct()
    {
        if (!Session::get())
            $this->redirect($_SERVER["HTTP_HOST"] . "/404" . $_SERVER["REQUEST_URI"]);
    }

    public function adminVerify($args) {
        $username = $args["route"]["arguments"]["post"]["username"];
        $password = $args["route"]["arguments"]["post"]["password"];
        return((new UserRepository())->verify($username, $password));
    }

    public function get($arguments) {
        if (isset($arguments["route"]["arguments"]["id"])) {
            $result = (new UserRepository())->findBy("id", $arguments["route"]["arguments"]["id"], "=");
            if (is_array($result) && count($result) === 1)
                return $result[0];
        }
        return (new UserRepository())->findAll();
    }

    public function create($arguments) {
        return [
            "result" => (new User($arguments["route"]["arguments"]["post"]))->save(),
            "status" => 200
        ];
    }
    public function update($arguments) {
        return [
            "result" => (new User($arguments["route"]["arguments"]["post"]))->save(),
            "status" => 200
        ];
    }

    public function delete($arguments) {
        return [
            "result" => Manager::getConnection("mysql")
                ->getQueryBuilder(User::class)
                ->delete(User::class)
                ->where([["id", "=", $arguments["route"]["arguments"]["id"]]])
                ->execute(),
            "status" => 301
        ];
    }
}