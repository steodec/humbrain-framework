<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\middleware;

use Humbrain\Framework\base\App;
use Humbrain\Framework\router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
class RouteMiddleware implements MiddlewareInterface
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $route = $this->router->match($request);
        if (!$route) :
            return $next($request);
        endif;
        $params = $route->getParams();
        $request = array_reduce(
            array_keys($params),
            function ($request, $key) use ($params) {
                return $request->withAttribute($key, $params[$key]);
            },
            $request
        );
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
