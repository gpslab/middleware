<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests;

use GpsLab\Component\Middleware\ValidatorMiddleware;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidatorMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->validator = $this->getMock(ValidatorInterface::class);
        $this->middleware = new ValidatorMiddleware($this->validator);
    }

    public function testHandleNoErrors()
    {
        $message = new \stdClass();
        $result = 'bar';

        $list = $this->getMock(ConstraintViolationListInterface::class);
        $list
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0))
        ;

        $next = function ($_message) use ($message, $result) {
            $this->assertEquals($message, $_message);

            return $result;
        };

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($message)
            ->will($this->returnValue($list))
        ;

        $this->assertEquals($result, $this->middleware->handle($message, $next));
    }

    /**
     * @expectedException \GpsLab\Component\Middleware\Exception\InvalidMessageException
     */
    public function testHandleHasErrors()
    {
        $message = new \stdClass();

        $list = $this->getMock(ConstraintViolationListInterface::class);
        $list
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1))
        ;

        $next = function () {
            $this->assertTrue(false, 'Next middleware must be never called in this case');
        };

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($message)
            ->will($this->returnValue($list))
        ;

        $this->middleware->handle($message, $next);
    }
}
