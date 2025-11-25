<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\ConfigProvider;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;

final class ConfigProviderTest extends AbstractCase
{
    /**
     * Test that config provider returns correct structure
     */
    public function testConfigProvider(): void
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
     * Test that invoke returns array with dependencies key
     */
    public function testInvokeReturnsDependenciesKey(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();

        self::assertArrayHasKey('dependencies', $config);
    }

    /**
     * Test that dependencies contains factories key
     */
    public function testDependenciesContainsFactoriesKey(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();
        $dependencies   = $config['dependencies'];
        assert(is_array($dependencies));

        self::assertArrayHasKey('factories', $dependencies);
    }

    /**
     * Test that getDependencies returns array with factories
     */
    public function testGetDependenciesReturnsFactories(): void
    {
        $configProvider = new ConfigProvider();
        $dependencies   = $configProvider->getDependencies();

        self::assertArrayHasKey('factories', $dependencies);
    }

    /**
     * Test that middleware class is registered in factories
     */
    public function testMiddlewareClassIsRegisteredInFactories(): void
    {
        $configProvider = new ConfigProvider();
        $dependencies   = $configProvider->getDependencies();
        $factories      = $dependencies['factories'];
        assert(is_array($factories));

        self::assertArrayHasKey(GeneratedByMiddleware::class, $factories);
    }

    /**
     * Test that factory class is correctly mapped
     */
    public function testFactoryClassIsCorrectlyMapped(): void
    {
        $configProvider = new ConfigProvider();
        $dependencies   = $configProvider->getDependencies();
        $factories      = $dependencies['factories'];
        assert(is_array($factories));

        self::assertSame(GeneratedByMiddlewareFactory::class, $factories[GeneratedByMiddleware::class]);
    }

    /**
     * Test that config provider can be instantiated
     */
    public function testConfigProviderCanBeInstantiated(): void
    {
        $configProvider = new ConfigProvider();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(ConfigProvider::class, $configProvider);
    }

    /**
     * Test that invoke returns same result as direct construction
     */
    public function testInvokeReturnsSameResultAsDirectConstruction(): void
    {
        $configProvider = new ConfigProvider();

        $result1 = $configProvider();
        $result2 = $configProvider->__invoke();

        self::assertSame($result1, $result2);
    }

    /**
     * Test that getDependencies is consistent with invoke result
     */
    public function testGetDependenciesIsConsistentWithInvoke(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();
        $dependencies   = $config['dependencies'];
        assert(is_array($dependencies));

        self::assertSame($configProvider->getDependencies(), $dependencies);
    }
}
