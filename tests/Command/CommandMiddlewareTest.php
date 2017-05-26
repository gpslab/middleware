<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Command;

use GpsLab\Component\Command\Bus\CommandBus;
use GpsLab\Component\Command\Command;
use GpsLab\Component\Middleware\Command\CommandMiddleware;

class CommandMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CommandBus
     */
    private $bus;

    /**
     * @var CommandMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->bus = $this->getMock(CommandBus::class);
        $this->middleware = new CommandMiddleware($this->bus);
    }

    public function testHandleCommand()
    {
        $command = $this->getMock(Command::class);
        $message = 'foo';
        $result = 'bar';

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);
            return $result;
        };
        $this->bus
            ->expects($this->once())
            ->method('handle')
            ->with($command)
            ->will($this->returnValue($message))
        ;

        $this->assertEquals($result, $this->middleware->handle($command, $next));
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
