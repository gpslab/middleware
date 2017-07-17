<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\DomainEvent;

use GpsLab\Component\Middleware\Middleware;
use GpsLab\Domain\Event\Bus\EventBus;
use GpsLab\Domain\Event\Event;

class DomainEventMiddleware implements Middleware
{
    /**
     * @var EventBus
     */
    private $bus;

    /**
     * @param EventBus $bus
     */
    public function __construct(EventBus $bus)
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
        if ($message instanceof Event) {
            return $next($this->bus->publish($message));
        }

        return $next($message);
    }
}
