<?php

namespace SlackUnfurl;

use Pimple\Container;

class CommandResolver
{
    /** @var Container */
    private $container;
    /** @var array */
    private $mapping;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function configure(array $mapping = [])
    {
        $this->mapping = $mapping;

        return $this;
    }

    public function resolve(string $command)
    {
        $resolved = $this->mapping[$command] ?? null;
        if (!$resolved) {
            throw new RuntimeException("Unable to resolve $command");
        }

        return $this->container[$resolved];
    }
}