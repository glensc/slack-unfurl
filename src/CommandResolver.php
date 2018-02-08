<?php

namespace Eventum\SlackUnfurl;

use InvalidArgumentException;
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
            throw new InvalidArgumentException("Unable to resolve $command");
        }

        return $this->container[$resolved];
    }
}