<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\base;

use GuzzleHttp\Psr7\ServerRequest;
use Humbrain\Framework\base\App;
use Humbrain\Framework\middleware\{DispatcherMiddleware, NotFoundMiddleware, RouteMiddleware};
use PHPUnit\Framework\TestCase;
use PHPUnit\Logging\Exception;

class AppTest extends TestCase
{
    private App $app;

    public function setUp(): void
    {
        $this->app = new App(dirname(__DIR__) . '/base/config.php');
        $this->app->addModule(Controller::class)
            ->registerController()
            ->pipe(RouteMiddleware::class)
            ->pipe(DispatcherMiddleware::class)
            ->pipe(NotFoundMiddleware::class);
    }

    /*
        public function testRun()
        {
            $response = $this->app->run(new ServerRequest('GET', '/', []));
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('toto', $response->getBody());
        }
    */

    public function testRenderer()
    {
        $response = $this->app->run(new ServerRequest('GET', '/twig', []));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<h1>toto</h1>', $response->getBody());
    }

    public function testNoMiddleWare()
    {
        $this->expectException(\Exception::class);
        $this->app = new App(dirname(__DIR__) . '/base/config.php');
        $this->app->addModule(Controller::class)
            ->registerController();
        $response = $this->app->run(new ServerRequest('GET', '/twig', []));
    }

    public function testBadMiddleWare()
    {
        $this->expectException(\Exception::class);
        $this->app = new App(dirname(__DIR__) . '/base/config.php');
        $this->app->addModule(Controller::class)
            ->registerController()
            ->pipe(\stdClass::class);
        $response = $this->app->run(new ServerRequest('GET', '/twig', []));
    }
}
