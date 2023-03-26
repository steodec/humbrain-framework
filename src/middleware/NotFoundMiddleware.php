<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
class NotFoundMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        return new Response(404, [], 'Erreur 404');
    }
}
