<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\router\attributes;

use Attribute;
use Humbrain\Framework\router\Method;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
#[Attribute]
class Route
{
    public string $routePath;
    public Method $method;

    public function __construct(string $routePath, Method $method = Method::GET)
    {
        $this->routePath = $routePath;
        $this->method = $method;
    }
}
