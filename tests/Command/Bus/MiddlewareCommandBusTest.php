<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Command\Bus;

use GpsLab\Component\Command\Command;
use GpsLab\Component\Middleware\Chain\MiddlewareChain;
use GpsLab\Component\Middleware\Command\Bus\MiddlewareCommandBus;

class MiddlewareCommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        /* @var $command \PHPUnit_Framework_MockObject_MockObject|Command */
        $command = $this->getMock(Command::class);

        /* @var $chain \PHPUnit_Framework_MockObject_MockObject|MiddlewareChain */
        $chain = $this->getMock(MiddlewareChain::class);
        $chain
            ->expects($this->once())
            ->method('run')
            ->with($command)
        ;

        $bus = new MiddlewareCommandBus($chain);
        $bus->handle($command);
    }
}
