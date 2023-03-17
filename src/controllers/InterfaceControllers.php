<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\controllers;

use Psr\Container\ContainerInterface;

interface InterfaceControllers
{
    public function __construct(?ContainerInterface $container);
}
