<?php

namespace Eventum\SlackUnfurl\ServiceProvider;

use Eventum\SlackUnfurl\Command;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CommandProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Container $app)
    {
        $app[Command\UrlVerification::class] = function ($app) {
            return new Command\UrlVerification();
        };

        $app[Command\EventCallback::class] = function ($app) {
            return new Command\EventCallback();
        };
    }
}