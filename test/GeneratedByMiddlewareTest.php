<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class GeneratedByMiddlewareTest extends AbstractCase
{
    public function testGeneratedByMiddleware(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'www.example.com',
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->getInstance()];
        $response     = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertEquals('78ac0e14-0f2b-529e-81e2-a0f50f6029c5', $actual);
    }

    public function testMissingServerVars(): void
    {
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertEquals('', $actual);
    }

    private function getInstance(): GeneratedByMiddleware
    {
        $container = new ServiceManager();
        $factory   = new GeneratedByMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
