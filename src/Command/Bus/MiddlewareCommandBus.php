<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Command\Bus;

use GpsLab\Component\Command\Bus\CommandBus;
use GpsLab\Component\Command\Command;
use GpsLab\Component\Middleware\Chain\MiddlewareChain;

class MiddlewareCommandBus implements CommandBus
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
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $this->chain->run($command);
    }
}
