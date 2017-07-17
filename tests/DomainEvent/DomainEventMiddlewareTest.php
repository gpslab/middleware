<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\DomainEvent;

use GpsLab\Component\Middleware\DomainEvent\DomainEventMiddleware;
use GpsLab\Domain\Event\Bus\EventBus;
use GpsLab\Domain\Event\Event;

class DomainEventMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventBus
     */
    private $bus;

    /**
     * @var DomainEventMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->bus = $this->getMock(EventBus::class);
        $this->middleware = new DomainEventMiddleware($this->bus);
    }

    public function testHandleCommand()
    {
        $event = $this->getMock(Event::class);
        $message = 'foo';
        $result = 'bar';

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);

            return $result;
        };
        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($event)
            ->will($this->returnValue($message))
        ;

        $this->assertEquals($result, $this->middleware->handle($event, $next));
    }

    public function testHandleNotCommand()
    {
        $message = 'foo';
        $result = 'bar';

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);

            return $result;
        };

        $this->assertEquals($result, $this->middleware->handle($message, $next));
    }
}
