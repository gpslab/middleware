<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Command;

use GpsLab\Component\Command\Bus\CommandBus;
use GpsLab\Component\Command\Command;
use GpsLab\Component\Middleware\Middleware;

class CommandMiddleware implements Middleware
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
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
        if ($message instanceof Command) {
            return $next($this->bus->handle($message));
        }

        return $next($message);
    }
}
