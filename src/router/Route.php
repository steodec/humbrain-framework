<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\router;

use DI\Attribute\Inject;
use DI\Container;
use Psr\Container\ContainerInterface;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
class Route
{
    private string $path;
    /**
     * @var array|callable
     */
    private mixed $callback;
    /**
     * @var string[]
     */
    private array $params;

    /**
     * @param Method $method
     * @param string $path
     * @param callable|string $callback
     */
    public function __construct(string $path, callable|array $callback, array $params = [])
    {
        $this->path = $path;
        $this->callback = $callback;
        $this->params = $params;
    }

    /**
     * @return string
     */
    final public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Route
     */
    final public function setPath(string $path): Route
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return callable|array
     */
    final public function getCallback(): callable|array
    {
        return $this->callback;
    }

    /**
     * @param callable|string $callback
     * @return Route
     */
    public function setCallback(callable|array $callback): Route
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
