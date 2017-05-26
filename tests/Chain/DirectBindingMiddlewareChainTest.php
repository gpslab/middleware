<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Chain;

use GpsLab\Component\Middleware\Chain\DirectBindingMiddlewareChain;
use GpsLab\Component\Middleware\Handler\MiddlewareHandler;

class DirectBindingMiddlewareChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectBindingMiddlewareChain
     */
    private $chain;

    protected function setUp()
    {
        $this->chain = new DirectBindingMiddlewareChain();
    }

    public function testRun()
    {
        $message = 'foo';
        $result1 = 'bar';
        $result2 = 'baz';

        /* @var $handler1 \PHPUnit_Framework_MockObject_MockObject|MiddlewareHandler */
        $handler1 = $this->getMock(MiddlewareHandler::class);
        $handler1
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($_message, callable $callable) use ($message, $result1) {
                $this->assertEquals($message, $_message);

                return $callable($result1);
            }))
        ;

        /* @var $handler2 \PHPUnit_Framework_MockObject_MockObject|MiddlewareHandler */
        $handler2 = $this->getMock(MiddlewareHandler::class);
        $handler2
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($_message, callable $callable) use ($result1, $result2) {
                $this->assertEquals($result1, $_message);

                return $callable($result2);
            }))
        ;

        $this->chain->append($handler2);
        $this->chain->append($handler1);
        $this->chain->append($handler2); // override middleware

        $this->assertEquals($result2, $this->chain->run($message));
    }

    public function testNoMiddlewares()
    {
        $this->assertEquals('foo', $this->chain->run('foo'));
    }
}
