<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class GeneratedByMiddlewareFactoryTest extends AbstractCase
{
    /**
     * Test that factory creates middleware instance
     */
    public function testFactoryCreatesMiddlewareInstance(): void
    {
        $container  = new ServiceManager();
        $factory    = new GeneratedByMiddlewareFactory();
        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(GeneratedByMiddleware::class, $middleware);
    }

    /**
     * Test that factory returns MiddlewareInterface
     */
    public function testFactoryReturnsMiddlewareInterface(): void
    {
        $container  = new ServiceManager();
        $factory    = new GeneratedByMiddlewareFactory();
        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * Test that factory can be instantiated
     */
    public function testFactoryCanBeInstantiated(): void
    {
        $factory = new GeneratedByMiddlewareFactory();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(GeneratedByMiddlewareFactory::class, $factory);
    }

    /**
     * Test that factory is invokable
     */
    public function testFactoryIsInvokable(): void
    {
        $factory = new GeneratedByMiddlewareFactory();

        // @phpstan-ignore-next-line
        self::assertTrue(is_callable($factory));
    }

    /**
     * Test that factory creates new instance each time
     */
    public function testFactoryCreatesNewInstanceEachTime(): void
    {
        $container   = new ServiceManager();
        $factory     = new GeneratedByMiddlewareFactory();
        $middleware1 = $factory($container);
        $middleware2 = $factory($container);

        self::assertNotSame($middleware1, $middleware2);
    }

    /**
     * Test that factory accepts any ContainerInterface
     */
    public function testFactoryAcceptsAnyContainerInterface(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new GeneratedByMiddlewareFactory();

        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(GeneratedByMiddleware::class, $middleware);
    }

    /**
     * Test that factory does not use container services
     */
    public function testFactoryDoesNotUseContainerServices(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::never())->method('get');
        $container->expects(self::never())->method('has');

        $factory = new GeneratedByMiddlewareFactory();
        $factory($container);
    }

    /**
     * Test that __invoke is callable
     */
    public function testInvokeMethodIsCallable(): void
    {
        $factory = new GeneratedByMiddlewareFactory();

        // @phpstan-ignore-next-line
        self::assertTrue(method_exists($factory, '__invoke'));
    }
}
