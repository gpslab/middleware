[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/middleware.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/middleware)
[![Total Downloads](https://img.shields.io/packagist/dt/gpslab/middleware.svg?maxAge=3600)](https://packagist.org/packages/gpslab/middleware)
[![Build Status](https://img.shields.io/travis/gpslab/middleware.svg?maxAge=3600)](https://travis-ci.org/gpslab/middleware)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/middleware.svg?maxAge=3600)](https://coveralls.io/github/gpslab/middleware?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/middleware.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/middleware/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/ed9115e0-283f-4799-993c-3777a044114d.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/ed9115e0-283f-4799-993c-3777a044114d)
[![StyleCI](https://styleci.io/repos/92312680/shield?branch=master)](https://styleci.io/repos/92312680)
[![License](https://img.shields.io/packagist/l/gpslab/middleware.svg?maxAge=3600)](https://github.com/gpslab/middleware)

# Infrastructure for use middleware in applications

![Request delegate pipeline](request-delegate-pipeline.png)

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require gpslab/middleware
```

## Middleware chain

`MiddlewareChain` contains a middlewares (`Middleware`) and sequentially apply them to the message by chain.

There are 3 implementations of the chain, but you can make your own.

* `DirectBindingMiddlewareChain` - direct binding;
* `ContainerMiddlewareChain` - [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md) container;
* `SymfonyContainerMiddlewareChain` - Symfony container *(Symfony 3.3
[implements](http://symfony.com/blog/new-in-symfony-3-3-psr-11-containers) a
[PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md))*.

## Handle command (CQRS)

Example usage middleware for handle Commands in CQRS.

```php
// middleware chain
$chain = new DirectBindingMiddlewareChain();

// add logger middleware
$chain->append(new LoggerMiddleware($logger));
// add validator middleware
$chain->append(new ValidatorMiddleware($validator));
// add middleware for handle command from origin command bus
$chain->append(new CommandMiddleware($command_bus));

// configure command bus
$bus = new MiddlewareCommandBus($chain);


// handle command
try {
    $bus->handle($my_command);
} catch(InvalidMessageException $e) {
    // show validation errors
    var_dump($e->getMessages());
}
```

## Handle query (CQRS)

Example usage middleware for handle Queries in CQRS.

```php
// middleware chain
$chain = new DirectBindingMiddlewareChain();

// add logger middleware
$chain->append(new LoggerMiddleware($logger));
// add validator middleware
$chain->append(new ValidatorMiddleware($validator));
// add middleware for handle query from origin query bus
$chain->append(new QueryMiddleware($query_bus));

// configure query bus
$bus = new MiddlewareQueryBus($chain);


// handle query
try {
    $bus->handle($my_query);
} catch (InvalidMessageException $e) {
    // show validation errors
    var_dump($e->getMessages());
}
```

## Handle Domain event

Example usage middleware for handle Domain events.

```php
// middleware chain
$chain = new DirectBindingMiddlewareChain();

// add logger middleware
$chain->append(new LoggerMiddleware($logger));
// add validator middleware
$chain->append(new ValidatorMiddleware($validator));
// add middleware for handle event from origin domain event bus
$chain->append(new DomainEventMiddleware($domain_event_bus));

// configure domain event bus
$bus = new MiddlewareDomainEventBus($chain);


// handle domain event
try {
    $bus->handle($my_event);
} catch (InvalidMessageException $e) {
    // show validation errors
    var_dump($e->getMessages());
}
```

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
