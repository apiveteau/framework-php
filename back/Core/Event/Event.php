<?php

namespace Core;

use Core\Event\Event as Base;

class Event implements Base
{
    static $event = [];

    /**
     * This function read the annotation entry and add event listener if find method containing the event marker
     */
    public static function linkEvent() {
        /**
         * Parse all function who had @ event marker
         */
        foreach (Kernel::getAnnotation()->getByMarker("event") as $classes => $methodElement) {
            foreach ($methodElement as $method => $event) {
                self::add($event, $classes . "::" . $method);
            }
        }
        Environment::addExecutionTime("event");
    }

    /**
     * This function add listener to an event
     * @param $eventName
     * @param $classnameAndMethod
     */
    public static function add($eventName, $classnameAndMethod = null)
    {
        /**
         * If event exist
         */
        if (!array_key_exists($eventName, self::$event)) {
            self::$event[$eventName] = [];
            /**
             * If add listener to the new event
             */
            if ($classnameAndMethod !== null)
                self::$event[$eventName][] = $classnameAndMethod;
        } else {
            /**
             * Add listener to event
             */
            if ($classnameAndMethod !== null)
                self::$event[$eventName][] = $classnameAndMethod;
        }
    }

    /**
     * This function execute all listener associated to an event
     * @param $eventName
     * @param $args
     * @return bool
     */
    public static function exec($eventName, &$args = null)
    {
        if (!is_string($eventName) || $eventName === "" || !array_key_exists($eventName, self::$event) || empty(self::$event[$eventName]))
            return false;
        /**
         * If there is listener for this event
         */
        foreach (self::$event[$eventName] as $listener) {
            /**
             * Executing it
             */
            $listener($args);
        }
        return true;
    }
}
