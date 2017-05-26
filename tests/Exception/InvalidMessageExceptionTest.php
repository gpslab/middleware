<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Tests\Exception;

use GpsLab\Component\Middleware\Exception\InvalidMessageException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidMessageExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ConstraintViolationListInterface
     */
    private $violations;

    protected function setUp()
    {
        $this->violations = $this->getMock(ConstraintViolationListInterface::class);
    }

    public function testNoViolation()
    {
        $code = 123;
        $previous = new \Exception();

        $exception = new InvalidMessageException($this->violations, $code, $previous);

        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals([], $exception->getMessages());
        $this->assertEquals($this->violations, $exception->getViolations());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
    }

    public function testOneViolation()
    {
        $message = 'foo';
        $property_path = 'bar';
        $code = 123;
        $previous = new \Exception();

        $violation = $this->getMock(ConstraintViolationInterface::class);
        $violation
            ->expects($this->atLeastOnce())
            ->method('getMessage')
            ->will($this->returnValue($message))
        ;
        $violation
            ->expects($this->once())
            ->method('getPropertyPath')
            ->will($this->returnValue($property_path))
        ;

        $violation_list = new ConstraintViolationList([
            $violation,
        ]);

        $exception = new InvalidMessageException($violation_list, $code, $previous);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals([$property_path => $message], $exception->getMessages());
        $this->assertEquals($violation_list, $exception->getViolations());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
    }

    public function testManyViolation()
    {
        $message1 = 'foo1';
        $property_path1 = 'bar1';
        $message2 = 'foo2';
        $property_path2 = 'bar2';
        $code = 123;
        $previous = new \Exception();

        $violation1 = $this->getMock(ConstraintViolationInterface::class);
        $violation1
            ->expects($this->atLeastOnce())
            ->method('getMessage')
            ->will($this->returnValue($message1))
        ;
        $violation1
            ->expects($this->once())
            ->method('getPropertyPath')
            ->will($this->returnValue($property_path1))
        ;

        $violation2 = $this->getMock(ConstraintViolationInterface::class);
        $violation2
            ->expects($this->atLeastOnce())
            ->method('getMessage')
            ->will($this->returnValue($message2))
        ;
        $violation2
            ->expects($this->once())
            ->method('getPropertyPath')
            ->will($this->returnValue($property_path2))
        ;

        $violation_list = new ConstraintViolationList([
            $violation1,
            $violation2,
        ]);

        $exception = new InvalidMessageException($violation_list, $code, $previous);

        $this->assertEquals($message1, $exception->getMessage());
        $this->assertEquals([
            $property_path1 => $message1,
            $property_path2 => $message2,
        ], $exception->getMessages());
        $this->assertEquals($violation_list, $exception->getViolations());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
    }
}
