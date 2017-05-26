<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Query;

use GpsLab\Component\Middleware\Query\QueryMiddleware;
use GpsLab\Component\Query\Dispatcher\QueryDispatcher;
use GpsLab\Component\Query\Query;

class QueryMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|QueryDispatcher
     */
    private $dispatcher;

    /**
     * @var QueryMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->dispatcher = $this->getMock(QueryDispatcher::class);
        $this->middleware = new QueryMiddleware($this->dispatcher);
    }

    public function testHandleQuery()
    {
        $query = $this->getMock(Query::class);
        $message = 'foo';
        $result = 'bar';

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);
            return $result;
        };
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->will($this->returnValue($message))
        ;

        $this->assertEquals($result, $this->middleware->handle($query, $next));
    }

    public function testHandleNotQuery()
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
