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
use GpsLab\Component\Middleware\Middleware;

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

        /* @var $middleware1 \PHPUnit_Framework_MockObject_MockObject|Middleware */
        $middleware1 = $this->getMock(Middleware::class);
        $middleware1
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($_message, callable $callable) use ($message, $result1) {
                $this->assertEquals($message, $_message);

                return $callable($result1);
            }))
        ;

        /* @var $middleware2 \PHPUnit_Framework_MockObject_MockObject|Middleware */
        $middleware2 = $this->getMock(Middleware::class);
        $middleware2
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($_message, callable $callable) use ($result1, $result2) {
                $this->assertEquals($result1, $_message);

                return $callable($result2);
            }))
        ;

        $this->chain->append($middleware2);
        $this->chain->append($middleware1);
        $this->chain->append($middleware2); // override middleware

        $this->assertEquals($result2, $this->chain->run($message));
    }

    public function testNoMiddlewares()
    {
        $this->assertEquals('foo', $this->chain->run('foo'));
    }
}
