<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedByMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GeneratedByMiddleware extends AbstractGeneratedByMiddleware
{
    /**
     * @var string
     */
    private const HEADER = 'X-Generated-By';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $server   = $request->getServerParams();

        return $response->withHeader(self::HEADER, $this->getServerId($server));
    }
}
