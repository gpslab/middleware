<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\DomainEvent\Bus;

use GpsLab\Component\Middleware\Chain\MiddlewareChain;
use GpsLab\Domain\Event\Aggregator\AggregateEvents;
use GpsLab\Domain\Event\Bus\EventBus;
use GpsLab\Domain\Event\Event;

class MiddlewareDomainEventBus implements EventBus
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
     * @param Event $event
     */
    public function publish(Event $event)
    {
        $this->chain->run($event);
    }

    /**
     * @param AggregateEvents $aggregator
     */
    public function pullAndPublish(AggregateEvents $aggregator)
    {
        foreach ($aggregator->pullEvents() as $event) {
            $this->publish($event);
        }
    }
}
