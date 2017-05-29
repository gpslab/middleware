<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests;

use GpsLab\Component\Middleware\LoggerMiddleware;
use Psr\Log\LoggerInterface;

class LoggerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var LoggerMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->middleware = new LoggerMiddleware($this->logger);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return [
            ['foo'],
            [123],
            [fopen(__FILE__, 'r')],
            [$this->getMock(self::class)],
            [$this],
        ];
    }

    /**
     * @dataProvider getMessages
     *
     * @param mixed  $message
     */
    public function testHandle($message)
    {
        $result = 'bar';

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);

            return $result;
        };

        $this->logger
            ->expects($this->at(0))
            ->method('debug')
            ->with('Started handling a message', ['message' => $message])
        ;
        $this->logger
            ->expects($this->at(1))
            ->method('debug')
            ->with('Finished handling a message', ['message' => $message, 'result' => $result])
        ;

        $this->assertEquals($result, $this->middleware->handle($message, $next));
    }
}
