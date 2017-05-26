<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Chain;

interface MiddlewareChain
{
    /**
     * @param mixed $message
     *
     * @return mixed
     */
    public function run($message);
}
