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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyContainerMiddlewareChain implements MiddlewareChain, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var string[]
     */
    private $middleware_ids = [];

    /**
     * @param string $service
     */
    public function registerService($service)
    {
        $index = array_search($service, $this->middleware_ids);

        // move existing middleware to end of chain
        if ($index !== false) {
            unset($this->middleware_ids[$index]);
            // correct array indexes
            $this->middleware_ids = array_values($this->middleware_ids);
        }

        $this->middleware_ids[] = $service;
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
        $middleware = $this->lazyLoad($index);

        if (!($middleware instanceof Middleware)) {
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
     * @return Middleware
     */
    private function lazyLoad($index)
    {
        if ($this->container instanceof ContainerInterface && isset($this->middleware_ids[$index])) {
            $middleware = $this->container->get($this->middleware_ids[$index]);

            if ($middleware instanceof Middleware) {
                return $middleware;
            }
        }

        return null;
    }
}
