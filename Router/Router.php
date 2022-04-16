<?php

namespace Router;

use Exception;

class Router
{
    /**
     * @var RouteInterface[]
     */
    private array $routes = [];

    public function addRoute(RouteInterface $route): Router
    {
        $methods = $route->getMethods();
        sort($methods);
        // избавляется от дублей
        $key = implode(',', $methods) . ':' . $route->getPath();
        $this->routes[$key] = $route;
        return $this;
    }

    /**
     * @param string $method
     * @param string $uri
     * @return RouteInterface
     * @throws Exception
     */
    public function match(string $method, string $uri): RouteInterface
    {
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if (!$route->hasMethods($method)) {
                continue;
            }

            if ($route->getPath() == $uri) {
                return $route;
            }
        }

        throw new Exception('Not Found', 404);
    }
}