<?php

namespace Tests\router;

use Humbrain\Framework\router\Route;
use PHPUnit\Framework\TestCase;

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/
class RouteTest extends TestCase
{
    public function testSetMethod()
    {
        $route = new Route('controller.index', [Controller::class, 'index']);
        $route->setPath('controller.index2');
        $this->assertEquals('controller.index2', $route->getPath());
        $route->setCallback([Controller::class, 'toto']);
        $this->assertEquals([Controller::class, 'toto'], $route->getCallback());
    }
}