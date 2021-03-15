<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
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
        $stack        = [
            new GeneratedByMiddleware(),
        ];
        $response     = Dispatcher::run($stack, $request);

        $string = $response->getHeaderLine('X-Generated-By');

        $this->assertEquals('78ac0e14-0f2b-529e-81e2-a0f50f6029c5', $string);
    }
}
