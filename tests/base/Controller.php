<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\base;

use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\attributes\Route;
use Humbrain\Framework\views\RendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    private RendererInterface $renderer;

    public function __construct(ContainerInterface $container)
    {
        $this->renderer = $container->get(RendererInterface::class);
        $this->renderer->addPath('home', __DIR__ . '/views/home');
        $this->renderer->addGlobal('title', 'toto');
    }

    #[Route('/')]
    final public function index(ServerRequestInterface $resquest): Response
    {
        return new Response(200, [], 'toto');
    }

    #[Route('/twig')]
    final public function twig(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@home/index', []);
    }
}
