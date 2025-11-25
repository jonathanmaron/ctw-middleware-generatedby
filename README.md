# Package "ctw/ctw-middleware-generatedby"

[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-generatedby/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-generatedby)
[![GitHub Actions](https://github.com/jonathanmaron/ctw-middleware-generatedby/actions/workflows/tests.yml/badge.svg)](https://github.com/jonathanmaron/ctw-middleware-generatedby/actions/workflows/tests.yml)
[![Scrutinizer Build](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/build-status/master)
[![Scrutinizer Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/?branch=master)

PSR-15 middleware to create a UUID v5 (Universally Unique Identifiers) and add it to the `X-Generated-By` header of the response. The UUID is created using the server IP address i.e. `$_SERVER['SERVER_ADDR']` and domain name of the application i.e. `$_SERVER['SERVER_NAME']`.

This functionality is useful when multiple applications servers are running behind a load balancer. By inspecting the `X-Generated-By` header, it is possible to find out exactly which application server processed the request without exposing its public IP addresses.

[middlewares/utils](https://packagist.org/packages/middlewares/utils) provides utility classes for working with PSR-15 and [ramsey/uuid](https://github.com/ramsey/uuid) provides UUID v5 generation.

## Installation

Install the middleware using Composer:

```bash
$ composer require ctw/ctw-middleware-generatedby
```

## Standalone Example

```php
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

$container = new ServiceManager();
$factory   = new GeneratedByMiddlewareFactory();

$generatedByMiddleware = $factory->__invoke($container);

$serverParams = [
    'SERVER_ADDR' => '1.1.1.1',
    'SERVER_NAME' => 'www.example.com',
];
$request      = Factory::createServerRequest('GET', '/', $serverParams);
$stack        = [
    $generatedByMiddleware,
];
$response     = Dispatcher::run($stack, $request);

$uuid = $response->getHeaderLine('X-Generated-By');

dump($uuid);  // 78ac0e14-0f2b-529e-81e2-a0f50f6029c5
```

## Example in [Mezzio](https://docs.mezzio.dev/)

The middleware has been extensively tested in Mezzio.

After using Composer to install, simply make the following changes to your application's configuration.

In `config/config.php`:

```php
$providers = [
    // [..]
    \Ctw\Middleware\GeneratedByMiddleware\ConfigProvider::class,
    // [..]    
];
```

In `config/pipeline.php`:

```php
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // [..]
    $app->pipe(GeneratedByMiddleware::class);
    // [..]
};
```

You can then test to ensure that the `X-Generated-At` header is in the returned HTTP headers with:

```bash
curl -I -k https://www.example.com.development
```

If you see the `X-Generated-At` header, the middleware is correctly installed:

```bash
date: Wed, 17 Mar 2021 05:59:26 GMT
x-generated-by: ce9f95cf-9ce3-5c0d-8c59-c579f2e474fb
content-type: text/html; charset=utf-8
```