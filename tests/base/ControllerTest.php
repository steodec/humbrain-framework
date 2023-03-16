<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\base;

use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\attributes\Route;
use Psr\Http\Message\ServerRequestInterface;

class ControllerTest
{
    public const DEFINITIONS = null;

    #[Route('/')]
    public final function index(ServerRequestInterface $resquest): Response
    {
        return new Response(200, [], 'toto');
    }
}
