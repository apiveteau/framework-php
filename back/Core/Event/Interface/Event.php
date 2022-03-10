<?php

namespace Core\Event;

interface Event
{
    /**
     * This function add listener to an event, if classname equal null, the event is just create
     * @param $eventName = "Namespace/class.method"
     * @param $classnameAndMethod : "My\Class->myMethod()"
     * @return mixed
     */
    static function add($eventName, $classnameAndMethod = null);

    /**
     * This function execute all listener associated to an event
     * @param $eventName
     * @param $args
     * @return mixed
     */
    static function exec($eventName, &$args = null);
}
