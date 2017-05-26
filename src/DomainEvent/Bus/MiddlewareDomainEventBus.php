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
use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;
use GpsLab\Domain\Event\Bus\EventBusInterface;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;

class MiddlewareDomainEventBus implements EventBusInterface
{
    /**
     * @var EventBusInterface
     */
    private $bus_publisher;

    /**
     * @var MiddlewareChain
     */
    private $chain;

    /**
     * @param EventBusInterface $bus_publisher
     * @param MiddlewareChain   $chain
     */
    public function __construct(EventBusInterface $bus_publisher, MiddlewareChain $chain)
    {
        $this->bus_publisher = $bus_publisher;
        $this->chain = $chain;
    }

    /**
     * @param EventInterface $event
     */
    public function publish(EventInterface $event)
    {
        $this->chain->run($event);
    }

    /**
     * @param AggregateEventsInterface $aggregator
     */
    public function pullAndPublish(AggregateEventsInterface $aggregator)
    {
        foreach ($aggregator->pullEvents() as $event) {
            $this->publish($event);
        }
    }

    /**
     * Get the list of every EventListener defined in the EventBus.
     * This might be useful for debug.
     *
     * @return ListenerInterface[]|ListenerCollection
     */
    public function getRegisteredEventListeners()
    {
        return $this->bus_publisher->getRegisteredEventListeners();
    }
}
