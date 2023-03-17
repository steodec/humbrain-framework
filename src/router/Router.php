<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\router;

use AltoRouter;
use DI\Container;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use Humbrain\Framework\router\attributes\Route as attribute;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
class Router
{
    /**
     * @var AltoRouter
     */
    private AltoRouter $router;
    private ContainerInterface $container;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    final public function match(ServerRequestInterface $request): ?Route
    {
        $routes = $this->router->match($request->getUri()->getPath(), $request->getMethod());
        if ($routes) :
            return new Route(
                $routes['name'],
                $routes['target'],
                $routes['params']
            );
        endif;
        return null;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string|null
     */
    final public function generateUri(string $name, array $params = []): ?string
    {
        try {
            return $this->router->generate($name, $params);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param string[] $controllers
     * @return void
     * @throws ReflectionException
     */
    final public function registerAll(array $controllers): void
    {
        foreach ($controllers as $controller) :
            $this->register($controller);
        endforeach;
    }

    /**
     * @param string $controller
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    final public function register(string $controller): void
    {
        $reflectionController = new ReflectionClass($controller);
        foreach ($reflectionController->getMethods() as $method) :
            $attributes = $method->getAttributes(attribute::class);
            foreach ($attributes as $attribute) :
                $route = $attribute->newInstance();
                if (!$route instanceof attribute) {
                    continue;
                }
                $controllerName = explode('\\', $controller);
                $controllerName = strtolower(end($controllerName));
                $name = $controllerName . '.' . $method->getName();
                $this->router->map(
                    $route->method->value,
                    $route->routePath,
                    [$controller, $method->getName()],
                    $name
                );
            endforeach;
        endforeach;
    }
}
