<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Query\Bus;

use GpsLab\Component\Middleware\Chain\MiddlewareChain;
use GpsLab\Component\Middleware\Query\Bus\MiddlewareQueryBus;
use GpsLab\Component\Query\Query;

class MiddlewareQueryBusTest extends \PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $result = 'foo';

        /* @var $query \PHPUnit_Framework_MockObject_MockObject|Query */
        $query = $this->getMock(Query::class);

        /* @var $chain \PHPUnit_Framework_MockObject_MockObject|MiddlewareChain */
        $chain = $this->getMock(MiddlewareChain::class);
        $chain
            ->expects($this->once())
            ->method('run')
            ->with($query)
            ->will($this->returnValue($result))
        ;

        $dispatcher = new MiddlewareQueryBus($chain);
        $this->assertEquals($result, $dispatcher->handle($query));
    }
}
