<?php


namespace Core\Template\Parts;


use Core\Environment;
use Core\Template;

class Func
{
    /**
     * Structure constructor.
     * @param $buffer
     * @param $args
     */
    public function __construct(&$buffer, &$args)
    {
        $this->convert($buffer, $args);
        $this->crop($buffer, $args);
    }

    /**
     * Apply crop on a given text
     * @param $buffer
     * @param $args
     */
    private function crop(&$buffer, &$args)
    {
        $matches = [];
        preg_match_all("/{crop\.(\d*):([\w|\w.\w+]*)}/", $buffer, $matches);
        $i = 0;
        foreach ($matches[2] as $vars) {
            $value = $this->getVarsValue($args, explode(".", $vars), 0);
            if ($value === false) {
                $value = $vars;
            }
            $buffer = str_replace("{crop." . $matches[1][$i] . ":" . $vars . "}", substr($value, 0, (int)$matches[1][$i] - 3) . "...", $buffer);
            $i++;
        }
    }

    /**
     * Apply the convert template functions
     * @param $buffer
     * @param $args
     */
    private function convert(&$buffer, &$args)
    {
        $matches = [];
        preg_match_all("/{convert\.(\w*):([\w|\w.\w+|\w\/\w+]*)}/", $buffer, $matches);
        $i = 0;
        foreach ($matches[2] as $vars) {
            $path = explode(".", $vars);
            $value = $this->getVarsValue($args, $path, 0);
            if ($value === false) {
                $value = $vars;
            }
            if ($value !== false) {
                switch ($matches[1][$i]) {
                    case "html":
                        $final = $this->convertHTML($value);
                        break;
                    case "json":
                        $final = json_encode($value);
                        break;
                    case "date":
                        $final =    date(Environment::getConfiguration("DATE_FORMAT"), (int)$value);
                        break;
                    case "urlencode":
                        $final = urlencode($value);
                        break;
                    case "urldecode":
                        $final = urldecode($value);
                        break;
                    default:
                        $final = $matches[1][$i];
                }
                $buffer = str_replace("{convert." . $matches[1][$i] . ":" . $vars . "}", $final, $buffer);

            } else
                $buffer = str_replace("{convert." . $matches[1][$i] . ":" . $vars . "} Convertion error", "{convert.html:false}", $buffer);
            $i++;
        }
    }

    /**
     * @param $value
     * @return string
     */
    private function convertHTML(&$value)
    {
        $html = "<div>";
        if (is_object($value)) {
            $value = Template::object_to_array($value);
        }
        foreach ($value as $k => $v) {
            if (is_array($v))
                $html .= $this->convertHTML($v);
            else
                $html .= "<span class='key'>" . $k . "</span><span class='value'>" . $v . "</span>";

        }
        return $html . "</div>";
    }

    /**
     * @param $args
     * @param $path
     * @param int $index
     * @return bool|string
     */
    private function getVarsValue($args, $path, $index = 0)
    {
        if (is_object($args[$path[$index]]))
            $args[$path[$index]] = Template::object_to_array($args[$path[$index]]);
        if (count($path) - 1 > $index) {
            return (isset($args[$path[$index]])) ?
                $this->getVarsValue($args[$path[$index]], $path, $index + 1)
                : false;
        } else
            return ($args[$path[$index]]) ?: false;
    }
}