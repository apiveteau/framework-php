<?php


namespace Tests;
require_once "Core/Loader/Interface/LoaderBase.php";
require_once "Core/Loader/Loader.php";

use Core\Environment;
use Core\Loader;
use Core\Security;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    private $token;
    
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        // Load core
        if (!defined("PATH_CORE"))
            define("PATH_CORE", __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Core" . DIRECTORY_SEPARATOR);
        Loader::explore(PATH_CORE, "Interface");
        Loader::explore(PATH_CORE, "", "Interface");
        $_SERVER["REMOTE_ADDR"] = "127.0.0.1";
        if (!defined("PATH_CACHE"))
            define("PATH_CACHE", __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Cache" . DIRECTORY_SEPARATOR);
        if (!defined("PATH_LOG"))
            define("PATH_LOG", __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Log" . DIRECTORY_SEPARATOR . "Test" . DIRECTORY_SEPARATOR);
        // Disabling log
        Environment::set("LOG_LEVEL", "0");
        Environment::set("TOKEN_SECRET_LENGTH", "20");
        Environment::set("TEMP_FORMAT", "Ym");
        $this->token = Security::generate("valid", "ImAValidConfigurationTokenSecret");
        Security::save($this->token);
        parent::__construct($name, $data, $dataName);
    }

    public function testGenerateToken() {
        $this->assertEquals(3, count($this->token));

        $this->assertFalse(Security::generate("", "test"));
        $this->assertFalse(Security::generate("test", ""));
        $this->assertFalse(Security::generate("", ""));
        $this->assertFalse(Security::generate("test", "test"));
        $this->assertFalse(Security::generate("test", "1234567890"));
        $this->assertTrue(is_array(Security::generate("test", "12345678901234567890")));
    }

    public function testCheckToken() {
        $unique = $this->token["unique"];
        $secret = "ImAValidConfigurationTokenSecret";
        $result = Security::check($unique, $secret);
        $this->assertTrue(is_array($result));
        $unique2 = $this->token["unique"] . "1";
        $secret2 = "ImAValidConfigurationTokenSecret";
        $result2 = Security::check($unique2, $secret2);
        $this->assertFalse($result2);
        $unique2 = $this->token["unique"];
        $secret2 = "ImAValidConfigurationTokenSecre";
        $result2 = Security::check($unique2, $secret2);
        $this->assertFalse($result2);
        $unique2 = $this->token["unique"];
        $secret2 = "";
        $result2 = Security::check($unique2, $secret2);
        $this->assertFalse($result2);
        $unique2 = "";
        $secret2 = "ImAValidConfigurationTokenSecret";
        $result2 = Security::check($unique2, $secret2);
        $this->assertFalse($result2);
    }
}