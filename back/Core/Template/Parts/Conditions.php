<?php


namespace Core\Template\Parts;


class Conditions
{
    public function __construct(&$buffer, &$args)
    {
        $this->build($buffer, $args);
    }

    /**
     * This function return the result of a condition in template
     * @param $condition
     * @param $args
     * @return mixed
     */
    private function exec(&$condition, &$args) {
        new Vars($condition, $args, "'");
        return eval('return ' . $condition . ';');
    }

    /**
     * This function build a simple if/else statement in template
     * @param $buffer
     * @param $args
     */
    private function build(&$buffer, &$args) {
        $regExp = '/\[if:([a-zA-Z0-9.=+\-* {}"\'!]*)](.*)\[if]/U';
        preg_match_all($regExp, $buffer, $matches,PREG_SET_ORDER, 0);
        foreach ($matches as $index => $match) {
            $full = $match[0];
            $condition = $match[1];
            $content = $match[2];
            $regThen = '/{then}(.*){:then}/U';
            preg_match_all($regThen, $content, $matchThen, PREG_SET_ORDER, 0);
            $then = $matchThen[0];
            $regElse = '/{else}(.*){:else}/U';
            preg_match_all($regElse, $content, $matchElse, PREG_SET_ORDER, 0);
            $else = $matchElse[0];
            if ($this->exec( $condition, $args)) {
                if (count($then[0]) > 0)
                    $buffer = str_replace($full, $then[1], $buffer);
            } else {
                if (count($else[0]) >  0)
                    $buffer = str_replace($full, $else[1], $buffer);
            }
        }
    }
}