<?php

namespace Framework\Middleware;

use Humbrain\Framework\middleware\MiddlewareInterface;
use Humbrain\Framework\router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware implements MiddlewareInterface
{

    /**
     * @var Router
     */
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $next($request);
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
