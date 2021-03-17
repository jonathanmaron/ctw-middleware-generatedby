<?php
declare(strict_types=1);

use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

require __DIR__ . '/../vendor/autoload.php';

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
