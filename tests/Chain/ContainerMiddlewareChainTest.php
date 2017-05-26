<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Chain;

use GpsLab\Component\Middleware\Chain\ContainerMiddlewareChain;
use GpsLab\Component\Middleware\Middleware;
use Psr\Container\ContainerInterface;

class ContainerMiddlewareChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    /**
     * @var ContainerMiddlewareChain
     */
    private $chain;

    protected function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->chain = new ContainerMiddlewareChain($this->container);
    }

    public function testRun()
    {
        $message = 'foo';
        $result1 = 'bar';
        $result2 = 'baz';

        $some_service = new \stdClass();
        $middleware1 = $this->getMock(Middleware::class);
        $middleware1
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($_message, callable $callable) use ($message, $result1) {
                $this->assertEquals($message, $_message);

                return $callable($result1);
            }))
        ;

        $middleware2 = $this->getMock(Middleware::class);
        $middleware2
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($_message, callable $callable) use ($result1, $result2) {
                $this->assertEquals($result1, $_message);

                return $callable($result2);
            }))
        ;

        $this->container
            ->expects($this->at(0))
            ->method('get')
            ->with('middleware1')
            ->will($this->returnValue($middleware1))
        ;
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('middleware2')
            ->will($this->returnValue($middleware2))
        ;
        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with('some_service')
            ->will($this->returnValue($some_service))
        ;

        $this->chain->registerService('middleware2');
        $this->chain->registerService('middleware1');
        $this->chain->registerService('middleware2'); // override service
        $this->chain->registerService('some_service');

        $this->assertEquals($result2, $this->chain->run($message));
    }

    public function testNoServices()
    {
        $this->assertEquals('foo', $this->chain->run('foo'));
    }
}
