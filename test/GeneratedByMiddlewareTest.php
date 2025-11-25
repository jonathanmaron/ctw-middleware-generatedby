<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Server\MiddlewareInterface;

final class GeneratedByMiddlewareTest extends AbstractCase
{
    /**
     * Test that middleware generates correct header from both server params
     */
    public function testGeneratedByMiddleware(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'www.example.com',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertSame('78ac0e14-0f2b-529e-81e2-a0f50f6029c5', $actual);
    }

    /**
     * Test that missing server vars return empty header
     */
    public function testMissingServerVars(): void
    {
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertSame('', $actual);
    }

    /**
     * Test that middleware implements MiddlewareInterface
     */
    public function testMiddlewareImplementsMiddlewareInterface(): void
    {
        $middleware = $this->getInstance();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * Test that only SERVER_ADDR generates a UUID
     */
    public function testOnlyServerAddrGeneratesUuid(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '192.168.1.1',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $actual
        );
    }

    /**
     * Test that only SERVER_NAME generates a UUID
     */
    public function testOnlyServerNameGeneratesUuid(): void
    {
        $serverParams = [
            'SERVER_NAME' => 'example.com',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $actual
        );
    }

    /**
     * Test that same server params always generate same UUID
     */
    public function testSameServerParamsGenerateSameUuid(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '10.0.0.1',
            'SERVER_NAME' => 'api.example.com',
        ];

        $request1  = Factory::createServerRequest('GET', '/', $serverParams);
        $request2  = Factory::createServerRequest('GET', '/different-path', $serverParams);
        $response1 = Dispatcher::run([$this->getInstance()], $request1);
        $response2 = Dispatcher::run([$this->getInstance()], $request2);

        self::assertSame($response1->getHeaderLine('X-Generated-By'), $response2->getHeaderLine('X-Generated-By'));
    }

    /**
     * Test that different server params generate different UUIDs
     */
    public function testDifferentServerParamsGenerateDifferentUuids(): void
    {
        $serverParams1 = [
            'SERVER_ADDR' => '10.0.0.1',
            'SERVER_NAME' => 'api.example.com',
        ];
        $serverParams2 = [
            'SERVER_ADDR' => '10.0.0.2',
            'SERVER_NAME' => 'api.example.com',
        ];

        $request1  = Factory::createServerRequest('GET', '/', $serverParams1);
        $request2  = Factory::createServerRequest('GET', '/', $serverParams2);
        $response1 = Dispatcher::run([$this->getInstance()], $request1);
        $response2 = Dispatcher::run([$this->getInstance()], $request2);

        self::assertNotSame(
            $response1->getHeaderLine('X-Generated-By'),
            $response2->getHeaderLine('X-Generated-By')
        );
    }

    /**
     * Test that case is normalized (lowercase)
     */
    public function testServerParamsAreLowercased(): void
    {
        $serverParams1 = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'WWW.EXAMPLE.COM',
        ];
        $serverParams2 = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'www.example.com',
        ];

        $request1  = Factory::createServerRequest('GET', '/', $serverParams1);
        $request2  = Factory::createServerRequest('GET', '/', $serverParams2);
        $response1 = Dispatcher::run([$this->getInstance()], $request1);
        $response2 = Dispatcher::run([$this->getInstance()], $request2);

        self::assertSame($response1->getHeaderLine('X-Generated-By'), $response2->getHeaderLine('X-Generated-By'));
    }

    /**
     * Test that whitespace is trimmed from server params
     */
    public function testServerParamsAreTrimmed(): void
    {
        $serverParams1 = [
            'SERVER_ADDR' => '  1.1.1.1  ',
            'SERVER_NAME' => '  www.example.com  ',
        ];
        $serverParams2 = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'www.example.com',
        ];

        $request1  = Factory::createServerRequest('GET', '/', $serverParams1);
        $request2  = Factory::createServerRequest('GET', '/', $serverParams2);
        $response1 = Dispatcher::run([$this->getInstance()], $request1);
        $response2 = Dispatcher::run([$this->getInstance()], $request2);

        self::assertSame($response1->getHeaderLine('X-Generated-By'), $response2->getHeaderLine('X-Generated-By'));
    }

    /**
     * Test various server address formats
     *
     * @return array<string, array{serverAddr: string}>
     */
    public static function serverAddressProvider(): array
    {
        return [
            'IPv4 address'       => [
                'serverAddr' => '192.168.1.1',
            ],
            'localhost IPv4'     => [
                'serverAddr' => '127.0.0.1',
            ],
            'public IPv4'        => [
                'serverAddr' => '8.8.8.8',
            ],
            'IPv6 address'       => [
                'serverAddr' => '::1',
            ],
            'full IPv6 address'  => [
                'serverAddr' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            ],
        ];
    }

    /**
     * Test that various server addresses generate valid UUIDs
     */
    #[DataProvider('serverAddressProvider')]
    public function testVariousServerAddressFormats(string $serverAddr): void
    {
        $serverParams = [
            'SERVER_ADDR' => $serverAddr,
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $actual
        );
    }

    /**
     * Test various server names
     *
     * @return array<string, array{serverName: string}>
     */
    public static function serverNameProvider(): array
    {
        return [
            'simple domain'      => [
                'serverName' => 'example.com',
            ],
            'with subdomain'     => [
                'serverName' => 'www.example.com',
            ],
            'multiple subdomains' => [
                'serverName' => 'api.v1.example.com',
            ],
            'localhost'          => [
                'serverName' => 'localhost',
            ],
            'with port'          => [
                'serverName' => 'localhost:8080',
            ],
            'domain with hyphens' => [
                'serverName' => 'my-example-site.com',
            ],
        ];
    }

    /**
     * Test that various server names generate valid UUIDs
     */
    #[DataProvider('serverNameProvider')]
    public function testVariousServerNames(string $serverName): void
    {
        $serverParams = [
            'SERVER_NAME' => $serverName,
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $actual
        );
    }

    /**
     * Test that response from handler is preserved
     */
    public function testResponseFromHandlerIsPreserved(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'example.com',
        ];
        $request = Factory::createServerRequest('GET', '/', $serverParams);
        $stack   = [
            $this->getInstance(),
            /**
             * @param mixed $request
             * @param mixed $next
             * @return \Psr\Http\Message\ResponseInterface
             */
            static function ($request, $next) {
                /** @var \Psr\Http\Server\RequestHandlerInterface $next */
                /** @var \Psr\Http\Message\ServerRequestInterface $request */
                $response = $next->handle($request);

                return $response->withHeader('X-Custom-Header', 'custom-value');
            },
        ];
        $response = Dispatcher::run($stack, $request);

        self::assertTrue($response->hasHeader('X-Generated-By'));
        self::assertTrue($response->hasHeader('X-Custom-Header'));
        self::assertSame('custom-value', $response->getHeaderLine('X-Custom-Header'));
    }

    /**
     * Test that header name is X-Generated-By
     */
    public function testHeaderNameIsXGeneratedBy(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '1.1.1.1',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        self::assertTrue($response->hasHeader('X-Generated-By'));
    }

    /**
     * Test with empty string server params
     */
    public function testEmptyStringServerParams(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '',
            'SERVER_NAME' => '',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertSame('', $actual);
    }

    /**
     * Test with null-like values (converted to string)
     */
    public function testNumericServerParams(): void
    {
        $serverParams = [
            'SERVER_ADDR' => 12345,
            'SERVER_NAME' => 67890,
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine('X-Generated-By');

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $actual
        );
    }

    private function getInstance(): GeneratedByMiddleware
    {
        $container = new ServiceManager();
        $factory   = new GeneratedByMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
