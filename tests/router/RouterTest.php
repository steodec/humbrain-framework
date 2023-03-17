<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\router;
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use DI\ContainerBuilder;
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
        $routes = $this->router->match(new ServerRequest('GET', '/toto', []));
        $this->assertEquals(null, $routes);
    }

    final public function testGenerateUri(): void
    {
        $routes = $this->router->generateUri('controller.index');
        $this->assertEquals('/', $routes);
    }
}
