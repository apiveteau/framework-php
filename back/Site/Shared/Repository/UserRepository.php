<?php


namespace Front\Repository;


use Core\Database\Manager;
use Core\Database\Repository;
use Core\Security\Session;
use Front\Model\User;

class UserRepository extends Repository
{
    public function __construct($classname = "")
    {
        $this->classname = User::class;
        parent::__construct($classname);
    }

    public function verify($username, $password) {
        $result = [];
        $user = $this->findBy("username", $username);
        if ($user && !empty($user) && password_verify($password, $user[0]->password)) {
            Session::set($username, $password);
            $_SESSION["id"] = $user[0]->id;
            $result["user"] = $user[0];
            $result["status"] = 200;
        } else {
            $result["user"] = false;
            $result["status"] = 301;
            $result["hash"] = password_hash($password, PASSWORD_BCRYPT);
            $result["error"] = [
                "username" => $username,
                "password" => $password
            ];
        }
        return $result;
    }
}