<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Chain;

use GpsLab\Component\Middleware\Middleware;

class DirectBindingMiddlewareChain implements MiddlewareChain
{
    /**
     * @var Middleware[]
     */
    private $middlewares = [];

    /**
     * @param Middleware $middleware
     */
    public function append(Middleware $middleware)
    {
        $index = array_search($middleware, $this->middlewares);

        // move existing middleware to end of chain
        if ($index !== false) {
            unset($this->middlewares[$index]);
            // correct array indexes
            $this->middlewares = array_values($this->middlewares);
        }

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
     * @return callable
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
