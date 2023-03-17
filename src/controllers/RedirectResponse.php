<?php

namespace Humbrain\Framework\controllers;

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/


class RedirectResponse extends Response
{

    public function __construct(string $url)
    {
        parent::__construct(301, ['Location' => $url]);
    }
}
