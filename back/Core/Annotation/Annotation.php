<?php


namespace Core;

use Core\Annotation\Annotation as Base;
use Exception;
use ReflectionClass;

class Annotation implements Base
{
    /**
     * This array contain all documentation of code
     * @var array
     */
    public $documentation = [];

    /**
     * This function parse all loaded classes and read these documentation
     */
    public function __construct()
    {
        /**
         * Get all namespace loaded
         */
        $loadedClasses = get_declared_classes();
        foreach ($loadedClasses as $class) {
            /**
             * If it is a class of framework (multi part namespace)
             */
            if (strpos($class, "\\") !== false) {
                try {
                    try {
                        /**
                         * Reflect this class to extract documentation
                         */
                        $reflectedClass = new ReflectionClass($class);
                        $namespace = explode("\\", $class)[0];
                        $this->document($class, $reflectedClass);
                        $this->documentation["classes"][$namespace][] = str_replace($namespace . "\\", "", $class);
                    } catch (Exception $exception) {
                        var_dump($exception);
                    }
                } catch (Exception $exception) {
                    var_dump($exception);
                }
            }
        }
        Environment::addExecutionTime("annotation");
    }

    /**
     * This function return classes annotation documentation filter by method or not
     * @param $classname
     * @param string $method
     * @return bool|mixed
     */
    public function getDocumentation($classname = "", $method = "")
    {
        if ($classname === "")
            return $this->documentation;
        if ($method !== "")
            return $this->documentation[$classname][$method] ?? false;
        else
            return $this->documentation[$classname];
    }

    /**
     * Parse all marker set into the doc of method
     * @param $marker
     * @return array
     */
    public function getByMarker($marker)
    {
        $result = [];
        /**
         * Read all annotation by classes
         */
        foreach ($this->documentation as $classes => $configuration) {
            /**
             * Read all annotation by method
             */
            foreach ($configuration as $method => $comment) {
                /**
                 * If comment exist
                 */
                if ($comment) {
                    /**
                     * If marker exist in this method
                     */
                    if (array_key_exists($marker, $comment)) {
                        foreach ($comment[$marker] as $markerValue) {
                            $result[$classes][$method] = trim($markerValue);
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * This function parse all method of class to extract documentation
     * @param $classname
     * @param ReflectionClass $reflectedClass
     */
    private function document($classname, ReflectionClass $reflectedClass)
    {
        $methods = $reflectedClass->getMethods();
        $properties = $reflectedClass->getProperties();
        foreach ($methods as $method) {
            $this->readDocComment($this->clearComment($method->getDocComment()), $this->documentation[$classname][$method->name]);
        }
        foreach ($properties as $property) {
            $this->readDocComment($this->clearComment($property->getDocComment()), $this->documentation[$classname]["properties"][$property->name]);
        }
    }

    /**
     * This function clear documentation string and return it as array
     * @param $comments
     * @return array
     */
    private function clearComment($comments)
    {
        $comments = explode(" * ", trim($comments));
        $i = 0;
        while ($i < count($comments)) {
            $comments[$i] = trim(str_replace("/**", "", str_replace("*/", "", $comments[$i])));
            if ($comments[$i] === "" || $comments[$i] === " ")
                unset($comments[$i]);
            $i++;
        }
        return array_values($comments);
    }

    /**
     * This function build the documentation array of a specific method
     * @param $comments
     * @param $classDocumentation
     */
    private function readDocComment($comments, &$classDocumentation)
    {
        $classDocumentation["description"] = "";
        foreach ($comments as $comment) {

            if (strpos($comment, "@") === 0) {
                $matches = [];
                /**
                 * Select by key=>value into the doc string
                 */
                preg_match_all("/@(\w*)\s(.*)/", $comment, $matches);
                if (count($matches[1])) {
                    $varsName = $matches[1][0];
                    $varsComment = $matches[2][0];
                    $classDocumentation[$varsName][] = $varsComment;
                }
            } else {
                /**
                 * If there is no marker, it is the description of method
                 */
                $classDocumentation["description"] .= $comment;
            }
        }
    }
}