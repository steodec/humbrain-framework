<?php

namespace Framework\Middleware;

use Humbrain\Framework\middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RoutePrefixedMiddleware implements MiddlewareInterface
{

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var string
     */
    private string $prefix;

    /**
     * @var MiddlewareInterface
     */
    private MiddlewareInterface $middleware;

    public function __construct(ContainerInterface $container, string $prefix, MiddlewareInterface $middleware)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->middleware = $middleware;
    }

    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (str_starts_with($path, $this->prefix)) {
            $middleware = $this->middleware;
            return $middleware->process($request, $next);
        }
        return $next($request);
    }
}
