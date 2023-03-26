<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\router;
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\router\Router;
use PHPUnit\Framework\TestCase;

/**
 * @property Router $router
 */
class RouterTest extends TestCase
{
    private Router $router;

    final public function setUp(): void
    {
        $this->router = new Router();
        $this->router->register(Controller::class);
    }

    final public function testRoute(): void
    {
        $routes = $this->router->match(new ServerRequest('GET', '/', []));
        $this->assertNotNull($routes, "Route not found");
    }

    final public function testNotFoundRoute(): void
    {
        $routes = $this->router->match(new ServerRequest('GET', '/toto400', []));
        $this->assertEquals(null, $routes);
    }

    final public function testGenerateUri(): void
    {
        $routes = $this->router->generateUri('controller.index');
        $this->assertEquals('/', $routes);
    }

    final public function testGenerateUriNull(): void
    {
        $routes = $this->router->generateUri('controller.index2');
        $this->assertEquals(null, $routes);
    }

    final public function testGetMethod(): void
    {
        $routes = $this->router->match(new ServerRequest('GET', '/', []));
        $this->assertEquals('controller.index', $routes->getPath());
        $this->assertEquals([Controller::class, 'index'], $routes->getCallback());
    }
}
