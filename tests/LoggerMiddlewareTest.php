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
        $resource = fopen(__FILE__, 'r');

        $class = new \stdClass();
        $class->foo = 1;
        $class->bar = 2;

        $mock = $this->getMock(self::class);
        $mock_class_name = get_class($mock);
        $mock_class_name = explode('_', $mock_class_name);

        return [
            [
                'foo',
                'Middleware handle a message',
                ['message' => 'foo'],
            ],
            [
                123,
                'Middleware handle a message',
                ['message' => 123],
            ],
            [
                $resource,
                'Middleware handle a resource',
                ['type' => get_resource_type($resource)],
            ],
            [
                $mock,
                sprintf('Middleware handle a "%s".', end($mock_class_name)),
                [],
            ],
            [
                $this,
                'Middleware handle a "LoggerMiddlewareTest".',
                [],
            ],
        ];
    }

    /**
     * @dataProvider getMessages
     *
     * @param mixed  $message
     * @param string $log_message
     * @param array  $context
     */
    public function testHandle($message, $log_message, array $context)
    {
        $result = 'bar';

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);

            return $result;
        };

        $this->logger
            ->expects($this->once())
            ->method('debug')
            ->with($log_message, $context)
        ;

        $this->assertEquals($result, $this->middleware->handle($message, $next));
    }
}
