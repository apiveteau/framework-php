<?php


namespace Tests;

require_once "Core/Event/Interface/Event.php";
require_once "Core/Event/Event.php";

use Core\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testAddEvent() {
        // Add event without default listener
        Event::add("test/event.1");
        // Add event with default listener
        Event::add("test/event.2", "Example\\Test::example");
        // Test if event configuration array is good
        $this->assertEquals([
            "test/event.1" => [],
            "test/event.2" => [
                "Example\\Test::example"
            ]
        ], Event::$event);
    }

    public function testExecEventValid() {
        $bool = false;
        Event::add("test/event.second", "Tests\EventTest::eventListener");
        Event::exec("test/event.second", $bool);
        $this->assertTrue($bool);
        Event::exec("test/event.second", $bool);
        $this->assertFalse($bool);
        Event::exec("test/event.second", $bool);
        $this->assertTrue($bool);
        Event::exec("test/event.second", $bool);
        $this->assertFalse($bool);
    }

    public function testExecEventNonValid() {
        Event::add("test/event.empty");
        // Bad event name given
        $this->assertFalse(Event::exec(null));
        $this->assertFalse(Event::exec(false));
        $this->assertFalse(Event::exec(42));
        $this->assertFalse(Event::exec(""));
        // Non existing event
        $this->assertFalse(Event::exec("*"));
        $this->assertFalse(Event::exec("test/event.nonExistingEvent"));
        // Event without listener
        $this->assertFalse(Event::exec("test/event.empty"));
    }

    /**
     * This function will be call by Event::exec
     * @param $args
     */
    static function eventListener(&$args) {
        $args = !$args;
    }
}