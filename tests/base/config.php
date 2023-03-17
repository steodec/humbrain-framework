<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

use Humbrain\Framework\router\Router;
use Humbrain\Framework\views\RendererInterface;
use Humbrain\Framework\views\TwigRendererFactory;
use function DI\create;
use function DI\factory;

return [
    Router::class => create(),
    RendererInterface::class => factory(TwigRendererFactory::class),
    "views.path" => __DIR__ . '/views',
];
