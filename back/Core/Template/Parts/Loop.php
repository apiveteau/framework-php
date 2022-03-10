<?php


namespace Core\Template\Parts;

use Core\Logger;
use Core\Template;

class Loop
{
    private $regex;

    public function __construct(&$buffer, &$args)
    {
        $this->regex = '/\[foreach:(\w*):([a-zA-Z0-9.]*)\sas\s([a-zA-Z0-9.]*)](.*?)\[foreach:+\1]/U';
        $this->getLoop($buffer, $args);
    }


    /**
     * This function make a foreach in templates.
     * Entry :
     * ....
     * [foreach:list as element]
     * <li>bou:'{key:element}:{element}'</li>
     * [foreach]
     * ....
     * ##
     * ....
     * [foreach:list.sublist as element]
     * <li>bou:'{key:element}:{element}'</li>
     * [foreach]
     * ....
     * @param &$buffer
     * @param &$args
     * @param $depth
     * @param $oldArgumentName = ""
     */
    private function getLoop(&$buffer, &$args, $depth = 0, $oldArgumentName = "") {
        Template::sectionalize($buffer);
        preg_match_all($this->regex, $buffer, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            $array = $this->getVarsValue($args, explode(".", $match[2]));
            Template::sectionalize($match[4]);
            $loopContent = $this->execALoop( $array, $match[1], $match[2], $match[3], $match[4]);
            $this->getLoop($loopContent, $args, $depth + 1, $match[2]);
            $buffer = str_replace($match[0], $loopContent, $buffer);
        }
    }

    /**
     * @param $args
     * @param $elements
     * @param $alias
     * @param $content
     * @return string
     */
    private function execALoop(&$elements, $namespace, $argumentName, &$alias, $content) {
        $slides = [];
        foreach ($elements as $key => $element) {
            $slides[] = $this->makeLoop($content, $argumentName . "." . $key, $alias, $namespace, $key);
        }
        return implode("", $slides);
    }

    /**
     * @param $content
     * @param $key
     * @param $alias
     * @param $index
     * @return mixed
     */
    private function makeLoop($content, $key, $alias, $namespace, $index) {
        $search = "{" . $alias . ".";
        $content = str_replace($search, "{" . $key . ".", $content);
        $search = ":" .$alias;
        $content = str_replace($search, ":" . $key, $content);
        $search = $namespace;
        $content = str_replace($search, $namespace . $index, $content);
        return $content;
    }

    /**
     * @param $args
     * @param $path
     * @param int $index
     * @param string $quote
     * @return mixed
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
            return $args[$path[$index]];
    }
}