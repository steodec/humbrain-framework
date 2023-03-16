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

    public final function setUp(): void
    {
        $this->router = new Router();
        $this->router->register(ControllerTest::class);
    }

    public final function testRoute(): void
    {
        $routes = $this->router->match(new ServerRequest('GET', '/', []));
        $this->assertNotNull($routes, "Route not found");
    }

    public final function testNotFoundRoute(): void
    {
        $routes = $this->router->match(new ServerRequest('GET', '/toto', []));
        $this->assertEquals(null, $routes);
    }

    public final function testGenerateUri(): void
    {
        $routes = $this->router->generateUri('controllertest.index');
        $this->assertEquals('/', $routes);
    }
}
