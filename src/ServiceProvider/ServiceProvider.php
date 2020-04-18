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
    public function register(Container $app): void
    {
        $app['unfurl.slack_api_token'] = getenv('SLACK_API_TOKEN');
        $app['unfurl.slack_verification_token'] = getenv('SLACK_VERIFICATION_TOKEN');
        $app['unfurl.app_prefix'] = getenv('APP_PREFIX') ?: '/';

        $app[SlackClient::class] = static function ($app) {
            return new SlackClient($app['unfurl.slack_api_token']);
        };

        $app[CommandResolver::class] = static function ($app) {
            return new CommandResolver($app);
        };

        $app[UnfurlController::class] = static function ($app) {
            return new UnfurlController(
                $app[CommandResolver::class],
                $app['unfurl.slack_verification_token']
            );
        };

        $app[InfoController::class] = static function ($app) {
            return new InfoController($app['unfurl.dispatcher']);
        };

        $app['unfurl.dispatcher'] = static function ($app) {
            return $app['dispatcher'];
        };
    }
}