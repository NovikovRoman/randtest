<?php

namespace Router;

interface RouteInterface
{
    public function getMethods(): array;

    public function hasMethods(string $method): bool;

    public function getPath(): string;
}