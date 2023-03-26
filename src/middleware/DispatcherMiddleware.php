<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\middleware;

use Exception;
use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\Route;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
class DispatcherMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) :
            return $next($request);
        endif;
        $callback = $route->getCallback();
        if (is_array($callback)) :
            $class = $this->container->get($callback[0]);
            $callback = [$class, $callback[1]];
        endif;
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) :
            return new Response(200, [], $response);
        elseif ($response instanceof ResponseInterface) :
            return $response;
        else :
            throw new Exception('The response is not a string or an instance of ResponseInterface');
        endif;
    }
}
