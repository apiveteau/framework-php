<?php


namespace Core\Security;

use Core\Environment;

class Session
{
    static function set($login, $password, $access = ["*"]) {
        $token = Token::generate($login, $password, $access, false);
        $_SESSION["connect"] = true;
        $_SESSION["login"] = $login;
        $_SESSION["secret"] = $password;
        $_SESSION["timeout"] = time() + (60 * 60 * 24 * (int)Environment::getConfiguration("SESSION_TIMEOUT"));
        $_SESSION["unique"] = $token["unique"];
        Token::save($token);
    }

    static function get() {
        if (!isset($_SESSION["unique"]))
            return false;
        if (time() > $_SESSION["timeout"]) {
            Token::destroy($_SESSION["unique"]);
        }
        return Token::check($_SESSION["unique"], $_SESSION["secret"]);
    }
}