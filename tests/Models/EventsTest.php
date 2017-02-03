<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Models;

use Mockery as m;
use Spiral\Models\DataEntity;
use Spiral\Models\Events\EntityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventsTest extends \PHPUnit_Framework_TestCase
{
    public function testEventsDispatcher()
    {
        $this->assertInstanceOf(EventDispatcherInterface::class, EventsTestEntity::events());
        $this->assertInstanceOf(EventDispatcherInterface::class, DataEntity::events());
        $this->assertNotSame(EventsTestEntity::events(), DataEntity::events());

        $class = new EventsTestEntity();
        $this->assertSame(EventsTestEntity::events(), $class->events());
    }

    public function testSetEventsDispatcher()
    {
        $events = m::mock(EventDispatcherInterface::class);
        EventsTestEntity::setEvents($events);

        $this->assertSame($events, EventsTestEntity::events());

        $class = new EventsTestEntity();
        $this->assertSame($events, $class->events());

        EventsTestEntity::setEvents(null);

        $this->assertInstanceOf(EventDispatcherInterface::class, $class->events());
        $this->assertNotSame($events, $class->events());
    }

    public function testFireEvent()
    {
        $class = new EventsTestEntity();
        $this->assertInstanceOf(EntityEvent::class, $class->doSomething());
        $this->assertSame($class, $class->doSomething()->getEntity());
    }
}

class EventsTestEntity extends DataEntity
{
    public function doSomething()
    {
        return $this->dispatch('test', new EntityEvent(
            $this
        ));
    }
}