<?php

namespace Router;

class Route implements RouteInterface
{
    private array $methods;
    private string $path;
    private string $controller;

    /**
     * @param string[] $methods
     * @param string $path
     * @param string $controller
     */
    public function __construct(array $methods, string $path, string $controller)
    {
        if (empty($methods)) {
            $methods = ['GET'];
        }

        $this->methods = array_map('strtoupper', $methods);
        $this->path = $path;
        $this->controller = $controller;
    }

    public function setController(string $controller): Route
    {
        $this->controller = $controller;
        return $this;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function hasMethods(string $method): bool
    {
        return in_array($method, $this->methods);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}