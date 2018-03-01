<?php

namespace SlackUnfurl\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SlackUnfurl\Command;
use SlackUnfurl\CommandResolver;
use SlackUnfurl\SlackClient;

class CommandProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app[Command\UrlVerification::class] = function () {
            return new Command\UrlVerification();
        };

        $app[Command\EventCallback::class] = function ($app) {
            return new Command\EventCallback($app[CommandResolver::class]);
        };

        $app[Command\LinkShared::class] = function ($app) {
            return new Command\LinkShared(
                $app[SlackClient::class],
                $app['unfurl.dispatcher'],
                $app['logger']
            );
        };
    }
}