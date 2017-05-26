<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware;

use GpsLab\Component\Middleware\Exception\InvalidMessageException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorMiddleware implements Middleware
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed    $message
     * @param callable $next
     *
     * @return mixed
     */
    public function handle($message, callable $next)
    {
        $violations = $this->validator->validate($message);

        if (count($violations)) {
            throw new InvalidMessageException($violations);
        }

        return $next($message);
    }
}
