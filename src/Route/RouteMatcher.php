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

        throw new RouteNotMatchedException("Route not matched for {$url}");
    }

    abstract protected function getRoutes(): array;
}