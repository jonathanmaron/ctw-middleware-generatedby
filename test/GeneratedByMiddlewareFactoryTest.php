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
     * Test that __invoke() returns a GeneratedByMiddleware instance when called with a container.
     */
    public function testInvokeWithContainerReturnsGeneratedByMiddleware(): void
    {
        $container  = new ServiceManager();
        $factory    = new GeneratedByMiddlewareFactory();
        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(GeneratedByMiddleware::class, $middleware);
    }

    /**
     * Test that __invoke() returns an instance implementing the PSR-15 MiddlewareInterface.
     */
    public function testInvokeWithContainerReturnsMiddlewareInterface(): void
    {
        $container  = new ServiceManager();
        $factory    = new GeneratedByMiddlewareFactory();
        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * Test that the factory can be constructed without arguments.
     */
    public function testConstructWithoutArgumentsCreatesFactory(): void
    {
        $factory = new GeneratedByMiddlewareFactory();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(GeneratedByMiddlewareFactory::class, $factory);
    }

    /**
     * Test that the factory is invokable as a callable.
     */
    public function testFactoryIsCallable(): void
    {
        $factory = new GeneratedByMiddlewareFactory();

        // @phpstan-ignore-next-line
        self::assertTrue(is_callable($factory));
    }

    /**
     * Test that __invoke() returns a distinct middleware instance on each call.
     */
    public function testInvokeCalledTwiceReturnsDistinctInstances(): void
    {
        $container   = new ServiceManager();
        $factory     = new GeneratedByMiddlewareFactory();
        $middleware1 = $factory($container);
        $middleware2 = $factory($container);

        self::assertNotSame($middleware1, $middleware2);
    }

    /**
     * Test that __invoke() accepts any PSR-11 ContainerInterface implementation.
     */
    public function testInvokeWithArbitraryContainerReturnsGeneratedByMiddleware(): void
    {
        $container = self::createStub(ContainerInterface::class);
        $factory   = new GeneratedByMiddlewareFactory();

        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(GeneratedByMiddleware::class, $middleware);
    }

    /**
     * Test that __invoke() never queries the container for services.
     */
    public function testInvokeDoesNotQueryContainerServices(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::never())->method('get');
        $container->expects(self::never())->method('has');

        $factory = new GeneratedByMiddlewareFactory();
        $factory($container);
    }

    /**
     * Test that the factory exposes a callable __invoke() method.
     */
    public function testInvokeMethodExistsOnFactory(): void
    {
        $factory = new GeneratedByMiddlewareFactory();

        // @phpstan-ignore-next-line
        self::assertTrue(method_exists($factory, '__invoke'));
    }
}
