<?php

namespace Humbrain\Framework\middleware;

use Humbrain\Framework\middleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MethodMiddleware implements MiddlewareInterface
{

    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) &&
            in_array($parsedBody['_method'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $next($request);
    }
}
