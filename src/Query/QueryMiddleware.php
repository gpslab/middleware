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
use GpsLab\Component\Query\Bus\QueryBus;
use GpsLab\Component\Query\Query;

class QueryMiddleware implements Middleware
{
    /**
     * @var QueryBus
     */
    private $bus;

    /**
     * @param QueryBus $bus
     */
    public function __construct(QueryBus $bus)
    {
        $this->bus = $bus;
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
            return $next($this->bus->handle($message));
        }

        return $next($message);
    }
}
