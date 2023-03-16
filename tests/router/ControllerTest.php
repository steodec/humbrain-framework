<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\router;

use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\attributes\Route;
use Psr\Http\Message\ServerRequestInterface;

class ControllerTest
{
    #[Route('/')]
    public final function index(ServerRequestInterface $resquest): Response
    {
        return new Response(200, [], 'toto');
    }
}
