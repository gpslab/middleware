<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Chain;

use GpsLab\Component\Middleware\Handler\MiddlewareHandler;

class DirectBindingMiddlewareChain implements MiddlewareChain
{
    /**
     * @var MiddlewareHandler[]
     */
    private $middlewares = [];

    /**
     * @param MiddlewareHandler $middleware
     */
    public function append(MiddlewareHandler $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @param mixed $message
     *
     * @return mixed
     */
    public function run($message)
    {
        return call_user_func($this->call(0), $message);
    }

    /**
     * @param int $index
     *
     * @return \Closure
     */
    private function call($index)
    {
        if (!isset($this->middlewares[$index])) {
            return function ($message) {
                return $message;
            };
        }

        $middleware = $this->middlewares[$index];

        return function ($message) use ($middleware, $index) {
            return $middleware->handle($message, $this->call($index + 1));
        };
    }
}
