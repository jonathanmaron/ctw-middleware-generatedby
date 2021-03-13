<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedByMiddleware;

use Psr\Container\ContainerInterface;

class GeneratedByMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): GeneratedByMiddleware
    {
        return new GeneratedByMiddleware();
    }
}
