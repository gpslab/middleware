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
use Psr\Container\ContainerInterface;

class ContainerMiddlewareChain implements MiddlewareChain
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $middleware_handler_ids = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $service
     */
    public function registerService($service)
    {
        $index = array_search($service, $this->middleware_handler_ids);

        // move existing middleware to end of chain
        if ($index !== false) {
            unset($this->middleware_handler_ids[$index]);
        }

        $this->middleware_handler_ids[] = $service;
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
        $middleware = $this->lazyLoad($index);

        if (!($middleware instanceof MiddlewareHandler)) {
            return function ($message) {
                return $message;
            };
        }

        return function ($message) use ($middleware, $index) {
            return $middleware->handle($message, $this->call($index + 1));
        };
    }

    /**
     * @param $index
     *
     * @return MiddlewareHandler
     */
    private function lazyLoad($index)
    {
        if (isset($this->middleware_handler_ids[$index])) {
            $handler = $this->container->get($this->middleware_handler_ids[$index]);

            if ($handler instanceof MiddlewareHandler) {
                return $handler;
            }
        }

        return null;
    }
}
