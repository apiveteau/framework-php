<?php


namespace Tests;

require_once "Core/Loader/Interface/LoaderBase.php";
require_once "Core/Loader/Loader.php";

use Core\Environment;
use Core\Loader;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->expectedConfiguration = [
            "SETTINGSWITHVALUE" =>"value",
            "SETTINGSWITHOUTVALUE" => "",
            "SETTINGSNEXTTOBLANKVALUE" => "working"
        ];
        if (!defined("PATH_CORE"))
            define("PATH_CORE", __DIR__ . "/../World Builder" . DIRECTORY_SEPARATOR . "Core");
        Loader::explore(PATH_CORE, "Interface");
        Loader::explore(PATH_CORE, "", "Interface");
        $this->env = new Environment(__DIR__ . DIRECTORY_SEPARATOR . ".env");
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @var Environment
     */
    private $env;

    /**
     * @var array $expectedConfiguration
     */
    private $expectedConfiguration;


    /**
     * Testing if a file .env is convert as a configuration array
     */
    public function testReadEnvFile() {
        $this->assertEquals($this->expectedConfiguration, $this->env->getConfiguration());
    }

    /**
     * Testing define env using set() function
     */
    public function testSetCustomEnv() {
        $this->env->set("CustomEnvSetting", "custom");
        $this->assertEquals("custom", $this->env->getConfiguration("CUSTOMENVSETTING"));
    }

    /**
     * Testing different get environment settings with getConfiguration
     */
    public function testGetEnv() {
        $this->env->set("CustomEnvSetting", "custom");
        $this->assertEquals(null, $this->env->getConfiguration("UNEXISTINGENVSETTING"));
        $this->assertEquals("custom", $this->env->getConfiguration("CUSTOMENVSETTING"));
        $this->assertEquals("", $this->env->getConfiguration("SETTINGSWITHOUTVALUE"));
    }
}