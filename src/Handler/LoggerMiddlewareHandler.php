<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Middleware\Handler;

use Psr\Log\LoggerInterface;

class LoggerMiddlewareHandler implements MiddlewareHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param mixed    $message
     * @param callable $next
     *
     * @return mixed
     */
    public function handle($message, callable $next)
    {
        switch (gettype($message)) {
            case 'object':
                $class_parts = explode('\\', get_class($message));
                $log_message = sprintf('Middleware handle a "%s".', end($class_parts));
                $context = json_decode(json_encode($message), true);
                break;
            case 'resource':
                $log_message = 'Middleware handle a resource';
                $context = ['type' => get_resource_type($message)];
                break;
            default:
                $log_message = 'Middleware handle a message';
                $context = ['message' => $message];
                break;
        }

        $this->logger->debug($log_message, $context);

        return $next($message);
    }
}
