<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\DomainEvent\Handler;

use GpsLab\Component\Middleware\Handler\MiddlewareHandler;
use GpsLab\Domain\Event\Bus\EventBusInterface;
use GpsLab\Domain\Event\EventInterface;

class DomainEventMiddlewareHandler implements MiddlewareHandler
{
    /**
     * @var EventBusInterface
     */
    private $bus;

    /**
     * @param EventBusInterface $bus
     */
    public function __construct(EventBusInterface $bus)
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
        if ($message instanceof EventInterface) {
            return $next($this->bus->publish($message));
        }

        return $next($message);
    }
}
