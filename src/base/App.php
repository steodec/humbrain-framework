<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\base;

use DI\ContainerBuilder;
use Exception;
use Humbrain\Framework\middleware\MiddlewareInterface;
use Humbrain\Framework\router\Route;
use Humbrain\Framework\router\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package Humbrain\Framework\base
 * @licence Apache-2.0
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
class App
{
    /**
     * @var ContainerInterface|null
     */
    private ?ContainerInterface $container = null;
    /**
     * @var string[]
     */
    private array $modules = [];
    private string $definitions;
    private int $index = 0;
    /**
     * @var string[]
     */
    private array $middlewares = [];

    /**
     * @param string $definitions
     */
    public function __construct(string $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Add module to app
     *
     * @param string $module
     * @return App
     */
    final public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Add middleware
     *
     * @param string $middleware
     * @return App
     */
    final public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Execute App
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    final public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->make($module, ['container' => $this->getContainer()]);
        }
        return $this->process($request);
    }

    /**
     * Return the ContainerInterface
     *
     * @return ContainerInterface
     * @throws Exception
     */
    private function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $builder->useAttributes(true);
            $builder->addDefinitions($this->definitions);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * Execute the next Middleware
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    final public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) :
            throw new Exception('Aucun middleware n\'a intercepté cette requête');
        elseif ($middleware instanceof MiddlewareInterface) :
            return $middleware->process($request, [$this, 'process']);
        endif;
        throw new Exception('Aucun middleware n\'a intercepté cette requête');
    }

    /**
     * Return the next Middleware
     *
     * @return object|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */

    private function getMiddleware(): ?object
    {
        if (array_key_exists($this->index, $this->middlewares)) :
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        endif;
        return null;
    }

    /**
     * Add controller to router
     *
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public function registerController(): self
    {
        $router = $this->getContainer()->get(Router::class);
        $router->registerAll($this->modules);
        return $this;
    }
}
