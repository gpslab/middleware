<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Query;

use GpsLab\Component\Middleware\Middleware;
use GpsLab\Component\Query\Dispatcher\QueryDispatcher;
use GpsLab\Component\Query\Query;

class QueryMiddleware implements Middleware
{
    /**
     * @var QueryDispatcher
     */
    private $dispatcher;

    /**
     * @param QueryDispatcher $dispatcher
     */
    public function __construct(QueryDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param mixed    $message
     * @param callable $next
     *
     * @return mixed
     */
    public function handle($message, callable $next)
    {
        if ($message instanceof Query) {
            return $next($this->dispatcher->dispatch($message));
        }

        return $next($message);
    }
}
