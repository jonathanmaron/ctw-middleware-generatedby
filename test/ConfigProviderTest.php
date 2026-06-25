<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\ConfigProvider;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;

final class ConfigProviderTest extends AbstractCase
{
    /**
     * Test that __invoke() returns the full dependency configuration structure.
     */
    public function testInvokeReturnsExpectedConfigStructure(): void
    {
        $configProvider = new ConfigProvider();

        $expected = [
            'dependencies' => [
                'factories' => [
                    GeneratedByMiddleware::class => GeneratedByMiddlewareFactory::class,
                ],
            ],
        ];

        self::assertSame($expected, $configProvider->__invoke());
    }

    /**
     * Test that __invoke() returns an array containing the dependencies key.
     */
    public function testInvokeReturnsArrayWithDependenciesKey(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();

        self::assertArrayHasKey('dependencies', $config);
    }

    /**
     * Test that the dependencies section produced by __invoke() contains a factories key.
     */
    public function testInvokeDependenciesContainsFactoriesKey(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();
        $dependencies   = $config['dependencies'];
        assert(is_array($dependencies));

        self::assertArrayHasKey('factories', $dependencies);
    }

    /**
     * Test that getDependencies() returns an array containing a factories key.
     */
    public function testGetDependenciesReturnsArrayWithFactoriesKey(): void
    {
        $configProvider = new ConfigProvider();
        $dependencies   = $configProvider->getDependencies();

        self::assertArrayHasKey('factories', $dependencies);
    }

    /**
     * Test that getDependencies() registers the middleware class within the factories map.
     */
    public function testGetDependenciesRegistersMiddlewareInFactories(): void
    {
        $configProvider = new ConfigProvider();
        $dependencies   = $configProvider->getDependencies();
        $factories      = $dependencies['factories'];
        assert(is_array($factories));

        self::assertArrayHasKey(GeneratedByMiddleware::class, $factories);
    }

    /**
     * Test that getDependencies() maps the middleware class to its factory class.
     */
    public function testGetDependenciesMapsMiddlewareToFactory(): void
    {
        $configProvider = new ConfigProvider();
        $dependencies   = $configProvider->getDependencies();
        $factories      = $dependencies['factories'];
        assert(is_array($factories));

        self::assertSame(GeneratedByMiddlewareFactory::class, $factories[GeneratedByMiddleware::class]);
    }

    /**
     * Test that the config provider can be constructed without arguments.
     */
    public function testConstructWithoutArgumentsCreatesConfigProvider(): void
    {
        $configProvider = new ConfigProvider();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(ConfigProvider::class, $configProvider);
    }

    /**
     * Test that invoking via __invoke() and via the magic callable form return identical results.
     */
    public function testInvokeAndMagicCallReturnIdenticalResult(): void
    {
        $configProvider = new ConfigProvider();

        $result1 = $configProvider();
        $result2 = $configProvider->__invoke();

        self::assertSame($result1, $result2);
    }

    /**
     * Test that getDependencies() returns the same array nested under the dependencies key of __invoke().
     */
    public function testGetDependenciesMatchesInvokeDependenciesSection(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();
        $dependencies   = $config['dependencies'];
        assert(is_array($dependencies));

        self::assertSame($configProvider->getDependencies(), $dependencies);
    }
}
