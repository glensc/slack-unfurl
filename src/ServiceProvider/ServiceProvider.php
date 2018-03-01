<?php

namespace SlackUnfurl\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SlackUnfurl\CommandResolver;
use SlackUnfurl\Controller\InfoController;
use SlackUnfurl\Controller\UnfurlController;
use SlackUnfurl\SlackClient;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['unfurl.slack_api_token'] = getenv('SLACK_API_TOKEN');
        $app['unfurl.slack_verification_token'] = getenv('SLACK_VERIFICATION_TOKEN');

        $app[SlackClient::class] = function ($app) {
            return new SlackClient($app['unfurl.slack_api_token']);
        };

        $app[CommandResolver::class] = function ($app) {
            return new CommandResolver($app);
        };

        $app[UnfurlController::class] = function ($app) {
            return new UnfurlController(
                $app[CommandResolver::class],
                $app['unfurl.slack_verification_token']
            );
        };

        $app[InfoController::class] = function ($app) {
            return new InfoController($app['unfurl.dispatcher']);
        };

        $app['unfurl.dispatcher'] = function ($app) {
            return $app['dispatcher'];
        };
    }
}