<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Chain;

use GpsLab\Component\Middleware\Chain\SymfonyContainerMiddlewareChain;
use GpsLab\Component\Middleware\Handler\MiddlewareHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyContainerMiddlewareChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    /**
     * @var SymfonyContainerMiddlewareChain
     */
    private $chain;

    protected function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->chain = new SymfonyContainerMiddlewareChain();
        $this->chain->setContainer($this->container);
    }

    public function testRun()
    {
        $message = 'foo';
        $result1 = 'bar';
        $result2 = 'baz';

        $some_service = new \stdClass();
        $handler1 = $this->getMock(MiddlewareHandler::class);
        $handler1
            ->expects($this->once())
            ->method('handle')
            ->with($message)
            ->will($this->returnCallback(function ($_message, callable $callable) use ($result1) {
                return $callable($result1);
            }))
        ;

        $handler2 = $this->getMock(MiddlewareHandler::class);
        $handler2
            ->expects($this->once())
            ->method('handle')
            ->with($result1)
            ->will($this->returnCallback(function ($_message, callable $callable) use ($result2) {
                return $callable($result2);
            }))
        ;

        $this->container
            ->expects($this->at(0))
            ->method('get')
            ->with('handler1')
            ->will($this->returnValue($handler1))
        ;
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('handler2')
            ->will($this->returnValue($handler2))
        ;
        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with('some_service')
            ->will($this->returnValue($some_service))
        ;

        $this->chain->registerService('handler2');
        $this->chain->registerService('handler1');
        $this->chain->registerService('handler2'); // override service
        $this->chain->registerService('some_service');

        $this->assertEquals($result2, $this->chain->run($message));
    }

    public function testNoServices()
    {
        $this->assertEquals('foo', $this->chain->run('foo'));
    }

    public function testNoContainer()
    {
        $chain = new SymfonyContainerMiddlewareChain();
        $this->assertEquals('foo', $chain->run('foo'));
    }
}
