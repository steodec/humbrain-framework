<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Tests\router;

use GuzzleHttp\Psr7\Response;
use Humbrain\Framework\router\attributes\Route;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    #[Route('/')]
    final public function index(ServerRequestInterface $resquest): Response
    {
        return new Response(200, [], 'toto');
    }

    #[Route('/toto')]
    final public function toto(ServerRequestInterface $resquest): Response
    {
        return new Response(200, [], 'toto');
    }
}
