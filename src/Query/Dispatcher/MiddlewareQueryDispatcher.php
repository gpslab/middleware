<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Query\Dispatcher;

use GpsLab\Component\Middleware\Chain\MiddlewareChain;
use GpsLab\Component\Query\Dispatcher\QueryDispatcher;
use GpsLab\Component\Query\Query;

class MiddlewareQueryDispatcher implements QueryDispatcher
{
    /**
     * @var MiddlewareChain
     */
    private $chain;

    /**
     * @param MiddlewareChain $chain
     */
    public function __construct(MiddlewareChain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * @param Query $query
     *
     * @return mixed
     */
    public function dispatch(Query $query)
    {
        return $this->chain->run($query);
    }
}
