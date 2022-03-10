<?php


namespace Core;

use Core\Template\Parts\Assets;
use Core\Template\Parts\Conditions;
use Core\Template\Parts\Loop;
use Core\Template\Parts\Func;
use Core\Template\Parts\Vars;
use Core\Template\Template as Base;

class Template implements Base
{
    private static $templatePath = "";
    private static $args = [];
    private static $baseTemplatePath = "";

    private static $varsBuilder;
    private static $conditionBuilder;
    private static $loopBuilder;
    private static $structureBuilder;
    private static $assetBuilder;

    private static function addBaseVars(&$args) {
        $args["__PAGE"]["configuration"] = Kernel::$routing->getCurrent()["route"];
        $args["__PAGE"]["configuration"]["template"] = self::$templatePath;
    }

    /**
     * @param $buffer
     * @return bool
     */
    private static function getCache(&$buffer) {
        if (file_exists(self::calcTemplatePath()) && Kernel::$context === "Production") {
            $buffer = file_get_contents(self::calcTemplatePath());
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    private static function calcTemplatePath() {
        return "Cache" . DIRECTORY_SEPARATOR . str_replace(DIRECTORY_SEPARATOR, "", strtoupper(md5(str_replace(self::$baseTemplatePath, "", self::$templatePath)))) . ".cache";
    }

    /**
     * This function render all template need from 1 master template
     * @param string $templatePath
     * @param array $args
     */
    static function render($templatePath = "index", &$args = [])
    {
        self::init($templatePath, $args);
        if (file_exists(self::$templatePath)) {
            ob_start(Template::class . "::boot");
            echo Files::read(self::$templatePath);
            ob_end_flush();
        } else {
            Response::setStatus(500);
            Logger::log("template", "Render a non existing template '" . self::$templatePath . "'", Logger::$ERROR_LEVEL);
        }
        /**
         * Exec event postProcess
         */
        Event::exec("core/template.postProcess");
    }

    private static function init(&$templatePath, &$args) {
        /**
         * Exec event preProcess
         */
        Event::exec("core/template.preProcess", $args);
        self::$args = self::object_to_array($args);
        self::$templatePath = PATH_ROOT . $templatePath . Environment::getConfiguration("TEMPLATE_EXT");
    }

    /**
     * This function initialize the template engine
     * @param $buffer
     * @return mixed
     */
    static function boot($buffer)
    {
        $buffer = preg_replace("/\r\n|\r|\n/", "", $buffer);
        Event::exec("core/template.preBuild", $buffer);
        self::build($buffer, self::$args);
        Event::exec("core/template.postBuild", $buffer);
        return $buffer;
    }

    /**
     * This function build a template
     * Cache include static format of a template so it don't include Loop / Condition / Vars
     * @param $buffer
     * @param $args
     */
    static function build(&$buffer, &$args) {
        self::addBaseVars($args);
        self::cacheBuilder($buffer);
        self::$loopBuilder = new Loop($buffer, $args);
        self::$assetBuilder = new Assets($buffer, $args);
        self::$conditionBuilder = new Conditions($buffer, $args);
        self::$varsBuilder = new Vars($buffer, $args);
        self::$structureBuilder = new Func($buffer, $args);
    }
    static function cacheBuilder(&$buffer) {
        /**
         * If we don't get cache or we are in Develop context
         */
        if (!self::getCache($buffer) || Kernel::$context === "Develop") {
            self::sectionalize($buffer);
            $buffer = str_replace("\n", "", $buffer);
            if (Kernel::$context === "Production") {
                Logger::log("template", "Save '" . self::calcTemplatePath() . "' cache file from " . self::$templatePath, Logger::$DEBUG_LEVEL);
                Files::put(self::calcTemplatePath(), $buffer);
            }
        }
    }
    /**
     * This function replace section call to the associated section recursively
     * @param $buffer
     */
    static function sectionalize(&$buffer)
    {
        $matches = [];
        preg_match_all("/{section:([a-zA-Z0-9\/]*)}/", $buffer, $matches);
        foreach ($matches[1] as $sectionPath) {
            $sectionPathContent = PATH_ROOT . $sectionPath
                . Environment::getConfiguration("TEMPLATE_EXT");
            if (!file_exists($sectionPathContent)) {
                Logger::log("template", "Try to render non-existing section " . $sectionPathContent, Logger::$DEBUG_LEVEL);
            } else {
                $sectionContent = Files::read($sectionPathContent);
                self::sectionalize($sectionContent);
                $buffer = str_replace("{section:" . $sectionPath . "}", $sectionContent, $buffer);
            }
            $buffer = preg_replace("/\r\n|\r|\n/", "", $buffer);
        }
    }

    /**
     * This function convert an object to an array recursively
     * @param $obj
     * @return array
     */
    static function object_to_array($obj) {
        $array = (array) $obj;
        foreach ($array as &$attribute) {
            if (is_object($attribute) || is_array($attribute)) $attribute = self::object_to_array($attribute);
        }
        return $array;
    }
}