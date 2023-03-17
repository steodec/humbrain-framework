<?php

namespace Framework\Middleware;

use Humbrain\Framework\middleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TrailingSlashMiddleware implements MiddlewareInterface
{

    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return (new \GuzzleHttp\Psr7\Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        return $next($request);
    }
}
