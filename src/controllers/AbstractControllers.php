<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\controllers;

use Psr\Container\ContainerInterface;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
abstract class AbstractControllers
{
    public const DEFINITIONS = null;

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
