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
use GpsLab\Domain\Event\Bus\EventBusInterface;
use GpsLab\Domain\Event\EventInterface;

class DomainEventMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventBusInterface
     */
    private $bus;

    /**
     * @var DomainEventMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->bus = $this->getMock(EventBusInterface::class);
        $this->middleware = new DomainEventMiddleware($this->bus);
    }

    public function testHandleCommand()
    {
        $event = $this->getMock(EventInterface::class);
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
