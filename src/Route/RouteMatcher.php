<?php

namespace SlackUnfurl\Route;

abstract class RouteMatcher
{
    /** @var array */
    protected $routes;

    public function match(string $url): array
    {
        foreach ($this->getRoutes() as $route => $pattern) {
            if (preg_match("!{$pattern}!", $url, $matches)) {
                return [$route, $matches];
            }
        }

        return null;
    }

    abstract protected function getRoutes(): array;
}