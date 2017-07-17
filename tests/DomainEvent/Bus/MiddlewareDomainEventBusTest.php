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
use GpsLab\Domain\Event\Aggregator\AggregateEvents;
use GpsLab\Domain\Event\Event;

class MiddlewareDomainEventBusTest extends \PHPUnit_Framework_TestCase
{
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
        $this->chain = $this->getMock(MiddlewareChain::class);
        $this->bus = new MiddlewareDomainEventBus($this->chain);
    }

    public function testPublish()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|Event */
        $event = $this->getMock(Event::class);

        $this->chain
            ->expects($this->once())
            ->method('run')
            ->with($event)
        ;

        $this->bus->publish($event);
    }

    public function testPullAndPublish()
    {
        /* @var $event1 \PHPUnit_Framework_MockObject_MockObject|Event */
        $event1 = $this->getMock(Event::class);
        /* @var $even2t \PHPUnit_Framework_MockObject_MockObject|Event */
        $event2 = $this->getMock(Event::class);

        /* @var $aggregator \PHPUnit_Framework_MockObject_MockObject|AggregateEvents */
        $aggregator = $this->getMock(AggregateEvents::class);
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
}
