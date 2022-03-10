<?php


namespace Core\Controller;


interface Controller
{
    public function redirect($uri);
    public function render($templatePath, $args = []);
}