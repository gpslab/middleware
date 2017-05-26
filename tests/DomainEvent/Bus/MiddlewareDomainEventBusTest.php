<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\DomainEvent\Bus;

use GpsLab\Component\Middleware\Chain\MiddlewareChain;
use GpsLab\Component\Middleware\DomainEvent\Bus\MiddlewareDomainEventBus;
use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;
use GpsLab\Domain\Event\Bus\EventBusInterface;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;

class MiddlewareDomainEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventBusInterface
     */
    private $event_bus;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MiddlewareChain
     */
    private $chain;

    /**
     * @var MiddlewareDomainEventBus
     */
    private $bus;

    protected function setUp()
    {
        $this->event_bus = $this->getMock(EventBusInterface::class);
        $this->chain = $this->getMock(MiddlewareChain::class);
        $this->bus = new MiddlewareDomainEventBus($this->chain, $this->event_bus);
    }

    public function testPublish()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event = $this->getMock(EventInterface::class);

        $this->chain
            ->expects($this->once())
            ->method('run')
            ->with($event)
        ;

        $this->bus->publish($event);
    }

    public function testPullAndPublish()
    {
        /* @var $event1 \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event1 = $this->getMock(EventInterface::class);
        /* @var $even2t \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event2 = $this->getMock(EventInterface::class);

        /* @var $aggregator \PHPUnit_Framework_MockObject_MockObject|AggregateEventsInterface */
        $aggregator = $this->getMock(AggregateEventsInterface::class);
        $aggregator
            ->expects($this->once())
            ->method('pullEvents')
            ->will($this->returnValue([
                $event1,
                $event2,
            ]))
        ;

        $this->chain
            ->expects($this->at(0))
            ->method('run')
            ->with($event1)
        ;
        $this->chain
            ->expects($this->at(1))
            ->method('run')
            ->with($event2)
        ;

        $this->bus->pullAndPublish($aggregator);
    }

    public function testGetRegisteredEventListeners()
    {
        $listeners = $this->getMock(ListenerCollection::class);

        $this->event_bus
            ->expects($this->once())
            ->method('getRegisteredEventListeners')
            ->will($this->returnValue($listeners))
        ;

        $this->assertEquals($listeners, $this->bus->getRegisteredEventListeners());
    }
}
