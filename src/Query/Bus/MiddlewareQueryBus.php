<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Query\Bus;

use GpsLab\Component\Middleware\Chain\MiddlewareChain;
use GpsLab\Component\Query\Bus\QueryBus;
use GpsLab\Component\Query\Query;

class MiddlewareQueryBus implements QueryBus
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
    public function handle(Query $query)
    {
        return $this->chain->run($query);
    }
}
