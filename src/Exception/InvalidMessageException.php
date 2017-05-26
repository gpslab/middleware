<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class InvalidMessageException extends ValidatorException
{
    /**
     * @var ConstraintViolationListInterface
     */
    protected $violations;

    /**
     * @param ConstraintViolationListInterface $violations
     * @param int                              $code
     * @param \Exception|null                  $previous
     */
    public function __construct(ConstraintViolationListInterface $violations, $code = 0, \Exception $previous = null)
    {
        $this->violations = $violations;
        $message = '';

        // get first message from violations
        foreach ($violations as $violation) {
            /* @var $violation ConstraintViolationInterface */
            $message = $violation->getMessage();
            break;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @return string[]
     */
    public function getMessages()
    {
        $messages = [];

        foreach ($this->violations as $violation) {
            /* @var $violation ConstraintViolationInterface */
            $messages[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $messages;
    }
}
