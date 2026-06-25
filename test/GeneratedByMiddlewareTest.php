<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddlewareFactory;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Server\MiddlewareInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;

final class GeneratedByMiddlewareTest extends AbstractCase
{
    private const string HEADER  = 'X-Generated-By';

    private const string PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    /**
     * Test that process() sets the header to the deterministic UUID v5 when both server params are present.
     */
    public function testProcessWithBothServerParamsSetsExpectedUuidHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '1.1.1.1',
            'SERVER_NAME' => 'www.example.com',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertSame('78ac0e14-0f2b-529e-81e2-a0f50f6029c5', $actual);
    }

    /**
     * Test that process() sets an empty header when no server params are supplied.
     */
    public function testProcessWithMissingServerParamsSetsEmptyHeader(): void
    {
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertSame('', $actual);
    }

    /**
     * Test that the middleware implements the PSR-15 MiddlewareInterface.
     */
    public function testProcessMiddlewareImplementsMiddlewareInterface(): void
    {
        $middleware = $this->getInstance();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * Test that process() generates a valid UUID v5 header when only SERVER_ADDR is present.
     */
    public function testProcessWithOnlyServerAddrGeneratesValidUuidHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '192.168.1.1',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(self::PATTERN, $actual);
    }

    /**
     * Test that process() generates a valid UUID v5 header when only SERVER_NAME is present.
     */
    public function testProcessWithOnlyServerNameGeneratesValidUuidHeader(): void
    {
        $serverParams = [
            'SERVER_NAME' => 'example.com',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(self::PATTERN, $actual);
    }

    /**
     * Test that process() produces the same UUID header for identical server params across requests.
     */
    public function testProcessWithSameServerParamsGeneratesSameUuidHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '10.0.0.1',
            'SERVER_NAME' => 'api.example.com',
        ];

        $request1  = Factory::createServerRequest('GET', '/', $serverParams);
        $request2  = Factory::createServerRequest('GET', '/different-path', $serverParams);
        $response1 = Dispatcher::run([$this->getInstance()], $request1);
        $response2 = Dispatcher::run([$this->getInstance()], $request2);

        self::assertSame($response1->getHeaderLine(self::HEADER), $response2->getHeaderLine(self::HEADER));
    }

    /**
     * Test that process() produces different UUID headers when the server params differ.
     */
    public function testProcessWithDifferentServerParamsGeneratesDifferentUuidHeader(): void
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

        self::assertNotSame($response1->getHeaderLine(self::HEADER), $response2->getHeaderLine(self::HEADER));
    }

    /**
     * Test that process() normalizes server param casing before generating the UUID header.
     */
    public function testProcessWithMixedCaseServerParamsGeneratesLowercasedUuidHeader(): void
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

        self::assertSame($response1->getHeaderLine(self::HEADER), $response2->getHeaderLine(self::HEADER));
    }

    /**
     * Test that process() trims surrounding whitespace from server params before generating the UUID header.
     */
    public function testProcessWithPaddedServerParamsGeneratesTrimmedUuidHeader(): void
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

        self::assertSame($response1->getHeaderLine(self::HEADER), $response2->getHeaderLine(self::HEADER));
    }

    /**
     * Provide a variety of server address formats expected to yield valid UUIDs.
     *
     * @return array<string, array{serverAddr: string}>
     */
    public static function serverAddressProvider(): array
    {
        return [
            'IPv4 address'      => [
                'serverAddr' => '192.168.1.1',
            ],
            'localhost IPv4'    => [
                'serverAddr' => '127.0.0.1',
            ],
            'public IPv4'       => [
                'serverAddr' => '8.8.8.8',
            ],
            'IPv6 address'      => [
                'serverAddr' => '::1',
            ],
            'full IPv6 address' => [
                'serverAddr' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            ],
        ];
    }

    /**
     * Test that process() generates a valid UUID v5 header for various SERVER_ADDR formats.
     */
    #[DataProvider('serverAddressProvider')]
    public function testProcessWithVariousServerAddrFormatsGeneratesValidUuidHeader(string $serverAddr): void
    {
        $serverParams = [
            'SERVER_ADDR' => $serverAddr,
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(self::PATTERN, $actual);
    }

    /**
     * Provide a variety of server name formats expected to yield valid UUIDs.
     *
     * @return array<string, array{serverName: string}>
     */
    public static function serverNameProvider(): array
    {
        return [
            'simple domain'       => [
                'serverName' => 'example.com',
            ],
            'with subdomain'      => [
                'serverName' => 'www.example.com',
            ],
            'multiple subdomains' => [
                'serverName' => 'api.v1.example.com',
            ],
            'localhost'           => [
                'serverName' => 'localhost',
            ],
            'with port'           => [
                'serverName' => 'localhost:8080',
            ],
            'domain with hyphens' => [
                'serverName' => 'my-example-site.com',
            ],
        ];
    }

    /**
     * Test that process() generates a valid UUID v5 header for various SERVER_NAME formats.
     */
    #[DataProvider('serverNameProvider')]
    public function testProcessWithVariousServerNamesGeneratesValidUuidHeader(string $serverName): void
    {
        $serverParams = [
            'SERVER_NAME' => $serverName,
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(self::PATTERN, $actual);
    }

    /**
     * Test that process() preserves the response and headers produced by the downstream handler.
     */
    public function testProcessWithDownstreamHandlerPreservesResponse(): void
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

        self::assertTrue($response->hasHeader(self::HEADER));
        self::assertTrue($response->hasHeader('X-Custom-Header'));
        self::assertSame('custom-value', $response->getHeaderLine('X-Custom-Header'));
    }

    /**
     * Test that process() always writes the X-Generated-By header onto the response.
     */
    public function testProcessAlwaysWritesGeneratedByHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '1.1.1.1',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        self::assertTrue($response->hasHeader(self::HEADER));
    }

    /**
     * Test that process() sets an empty header when both server params are empty strings.
     */
    public function testProcessWithEmptyStringServerParamsSetsEmptyHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '',
            'SERVER_NAME' => '',
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertSame('', $actual);
    }

    /**
     * Test that process() casts numeric (scalar) server params to string before generating the UUID header.
     */
    public function testProcessWithNumericServerParamsGeneratesValidUuidHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => 12345,
            'SERVER_NAME' => 67890,
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertNotEmpty($actual);
        self::assertMatchesRegularExpression(self::PATTERN, $actual);
    }

    /**
     * Test that process() sets an empty header when whitespace-only server params trim to nothing.
     */
    public function testProcessWithWhitespaceOnlyServerParamsSetsEmptyHeader(): void
    {
        $serverParams = [
            'SERVER_ADDR' => '   ',
            'SERVER_NAME' => "\t\n",
        ];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $response = Dispatcher::run([$this->getInstance()], $request);

        $actual = $response->getHeaderLine(self::HEADER);

        self::assertSame('', $actual);
    }

    /**
     * Test that process() swallows a UUID generation failure and sets an empty header.
     *
     * Replaces the global Ramsey UUID factory with one whose uuid5() throws, exercising the
     * catch (Exception) branch in AbstractGeneratedByMiddleware::getServerId().
     */
    public function testProcessWhenUuidGenerationThrowsSetsEmptyHeader(): void
    {
        $originalFactory   = Uuid::getFactory();
        $throwingFactory   = $this->createThrowingUuidFactory();

        Uuid::setFactory($throwingFactory);

        try {
            $serverParams = [
                'SERVER_ADDR' => '1.1.1.1',
                'SERVER_NAME' => 'www.example.com',
            ];
            $request  = Factory::createServerRequest('GET', '/', $serverParams);
            $response = Dispatcher::run([$this->getInstance()], $request);

            $actual = $response->getHeaderLine(self::HEADER);

            self::assertSame('', $actual);
        } finally {
            Uuid::setFactory($originalFactory);
        }
    }

    private function getInstance(): GeneratedByMiddleware
    {
        $container = new ServiceManager();
        $factory   = new GeneratedByMiddlewareFactory();

        return $factory->__invoke($container);
    }

    /**
     * Build a UUID factory stub whose uuid5() throws, simulating a UUID generation failure.
     */
    private function createThrowingUuidFactory(): UuidFactoryInterface
    {
        $factory = self::createStub(UuidFactoryInterface::class);
        $factory->method('uuid5')
            ->willThrowException(new Exception('Simulated UUID v5 generation failure.'));

        return $factory;
    }
}
