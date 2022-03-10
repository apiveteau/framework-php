<?php

namespace Core\Template\Parts;

use Core\Environment;
use Core\Kernel;
use Core\Logger;
use Core\Template;

class Vars
{
    /**
     * Vars constructor.
     * @param $buffer
     * @param $args
     * @param string $quote
     */
    public function __construct(&$buffer, &$args, $quote = "")
    {
        /**
         * Show fully a var, only available in develop context
         */
        if (Kernel::$context === "Develop") {
            self::debug($buffer, $args);
        }
        $this->setVars($buffer, $args, $quote);
    }

    /**
     * This function add the debug to frontend, this flag show a vars in html (each kind of var)
     */
    private function debug(&$buffer, &$args) {
        $matches = [];
        preg_match_all("/{debug:(.*?)}/s", $buffer, $matches);
        $i = 0;
        while ($i < count($matches[1])) {
            $args["execution_time"] = Environment::getConfiguration("TIME");
            switch ($matches[1][$i]) {
                case "__args":
                    Logger::log("template", "Debugging all vars", Logger::$WARNING_LEVEL);
                    $buffer = str_replace("{debug:" . $matches[1][$i] . "}", $this->showArray(Template::object_to_array($args)), $buffer);
                    break;
                default:
                    Logger::log("template", "Debugging " . $matches[1][$i] . " vars", Logger::$DEBUG_LEVEL);
                    $buffer = str_replace("{debug:" . $matches[1][$i] . "}", $this->showArray(Template::object_to_array($args[$matches[1][$i]])), $buffer);
                    break;
            }
            $i++;
        }
    }

    /**
     * This function replace all the custom vars {WORD} / {WORD.WORD.WORD....}
     * @param $buffer
     * @param $args
     * @param string $quote
     */
    private function setVars(&$buffer, &$args, $quote = "") {
        $matches = [];
        preg_match_all("/{([\w|\w.\w+]*)}/", $buffer, $matches);
        foreach ($matches[1] as $vars) {
            $path = explode(".", $vars);
            if (($value = $this->getVarsValue($args, $path, 0, $quote)) === false)
                Logger::log("template", "Try to render a non-existing or unvalid vars '" . $vars . "'", Logger::$ERROR_LEVEL);
            else
                $buffer = str_replace("{" . $vars . "}", $value, $buffer);
        }
    }

    /**
     * @param $args
     * @param $path
     * @param int $index
     * @param string $quote
     * @return bool|string
     */
    private function getVarsValue($args, $path, $index = 0, $quote = "")
    {
        if (!isset($args[$path[$index]]))
            return false;
        if (is_object($args[$path[$index]]))
            $args[$path[$index]] = Template::object_to_array($args[$path[$index]]);
        if (count($path) - 1 > $index) {
            return (isset($args[$path[$index]])) ?
                $this->getVarsValue($args[$path[$index]], $path, $index + 1, $quote)
                : false;
        } else
            return $quote . str_replace($quote, "\\" . $quote, $args[$path[$index]]) . $quote;
    }

    /**
     * This function show recursively an array as ul>li in html string
     * @param $array
     * @return string
     */
    private function showArray($array) {
        $result = "<ul class='array'>";
        $keys = array_keys($array);
        $i = 0;
        while ($i < count($array)) {
            if (is_array($array[$keys[$i]]))
                $result .= "<li><span class='key'>" . $keys[$i] . "</span>" . $this->showArray($array[$keys[$i]]) . "</li>";
            else
                $result .= "<li><span class='key'>" . $keys[$i] . "</span><span class='value'>" . $array[$keys[$i]] . "</span></li>";
            $i++;
        }
        return $result . "</ul>";
    }
}