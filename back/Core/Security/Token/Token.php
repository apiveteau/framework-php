<?php


namespace Core\Security;


use Core\Environment;
use Core\Event;
use Core\Files;
use Core\Logger;

class Token
{
    /**
     * Generate a secure token
     * @param $name
     * @param $secret
     * @param array $access
     * @return array|bool
     */
    static function generate($name, $secret, $access = ["*"], $checkLength = true) {
        if ($name === "" || $secret === "" || ($checkLength && strlen($secret) < (int)Environment::getConfiguration("TOKEN_SECRET_LENGTH")))
            return false;
        $token = [];
        $token["unique"] = self::getUnique($name);
        $token["secret"] = password_hash($secret, PASSWORD_DEFAULT);
        $token["access"] = $access;
        Logger::log("security", "Generate " . $token["unique"] . " token", Logger::$DEFAULT_LEVEL);
        Event::exec("core/token.generate", $token);
        return $token;
    }

    /**
     * Save a token
     * @param $token
     * @return bool
     */
    static function save($token) {
        if (count($token) === 3 && self::checkTokenValue($token["unique"]) && self::checkTokenValue($token["secret"])) {
            Event::exec("core/token.save", $token);
            Logger::log("security", "Save " . $token["unique"] . " token", 3);
            Files::put(self::getTokenPath($token["unique"]), $token["unique"] . "|" . $token["secret"] . "\n" . implode(":", $token["access"]), true);
            return true;
        }
        return false;
    }

    /**
     * Check if a token exist
     * @param $unique
     * @param $secret
     * @return bool|array $token
     */
    static function check($unique, $secret) {
        $path = self::getTokenPath($unique);
        if (!file_exists($path)) {
            Logger::log("security", "[" . $_SERVER["REMOTE_ADDR"] . "] Try to auth with a non-existing token " . $unique, Logger::$WARNING_LEVEL);
            return false;
        }
        $tokenContent = Files::read($path);
        $identity = explode("|", explode("\n", $tokenContent)[0]);
        if ($identity[0] === $unique && password_verify($secret, $identity[1])) {
            Logger::log("security", "[" . $_SERVER["REMOTE_ADDR"] . "] Token authentication successful for " . $identity[0], Logger::$DEFAULT_LEVEL);
            return [
                "unique" => $identity[0],
                "secret" => $identity[1],
                "access" => explode(":", explode("\n", $tokenContent)[1])
            ];
        }
        Logger::log("security", "[" . $_SERVER["REMOTE_ADDR"] . "] Token authentication failed for " . $unique . " with given password " . $secret, Logger::$WARNING_LEVEL);
        return false;
    }

    /**
     * @param $unique
     */
    static function destroy($unique) {
        Files::delete(self::getTokenPath($unique));
    }

    /**
     * Return token path
     * @param $unique
     * @return string
     */
    private static function getTokenPath($unique) {
        return PATH_CACHE . "security" . DIRECTORY_SEPARATOR
            . "token" . DIRECTORY_SEPARATOR
            . strtoupper(md5($unique)) . ".token";
    }

    /**
     * Generate a unique token name
     * @param $name
     * @return string
     */
    private static function getUnique($name) {
        return self::hash($_SERVER["REMOTE_ADDR"] . session_id() . $name);
    }

    /**
     * Hash string
     * @param $string
     * @return string
     */
    private static function hash($string) {
        return hash("sha256", $string);
    }

    /**
     * Check if value is valid
     * @param $value
     * @return bool
     */
    private static function checkTokenValue($value) {
        if ($value !== null && $value !== "" && is_string($value))
            return true;
        else
            return false;
    }
}