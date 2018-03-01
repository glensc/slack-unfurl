<?php

namespace SlackUnfurl\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SlackUnfurl\CommandResolver;
use SlackUnfurl\Controller\UnfurlController;
use SlackUnfurl\SlackClient;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app[SlackClient::class] = function ($app) {
            $apiToken = getenv('SLACK_API_TOKEN');

            return new SlackClient($apiToken);
        };

        $app[CommandResolver::class] = function ($app) {
            return new CommandResolver($app);
        };

        $app[UnfurlController::class] = function ($app) {
            return new UnfurlController(
                $app[CommandResolver::class],
                getenv('SLACK_VERIFICATION_TOKEN')
            );
        };

        $app['unfurl.dispatcher'] = function ($app) {
            return $app['dispatcher'];
        };
    }
}