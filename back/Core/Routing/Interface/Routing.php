<?php


namespace Core\Routing;


interface Routing
{

    /**
     * This function return the current route and the status code
     * @return array
     */
    public function getCurrent();

}