<?php

namespace Eventum\SlackUnfurl\ServiceProvider;

use Eventum\SlackUnfurl\CommandResolver;
use Eventum\SlackUnfurl\Controller\UnfurlController;
use Eventum\SlackUnfurl\SlackClient;
use Eventum\SlackUnfurl\Unfurler;
use Eventum_RPC;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['eventum.rpc_url'] = getenv('EVENTUM_RPC_URL');
        $app['eventum.username'] = getenv('EVENTUM_USERNAME');
        $app['eventum.access_token'] = getenv('EVENTUM_ACCESS_TOKEN');

        $app[Eventum_RPC::class] = function ($app) {
            $client = new Eventum_RPC($app['eventum.rpc_url']);
            $client->setCredentials($app['eventum.username'], $app['eventum.access_token']);

            return $client;
        };

        $app[SlackClient::class] = function ($app) {
            $apiToken = getenv('SLACK_API_TOKEN');

            return new SlackClient($apiToken);
        };

        $app[CommandResolver::class] = function ($app) {
            return new CommandResolver($app);
        };

        $app[Unfurler::class] = function ($app) {
            return new Unfurler(
                $app[Eventum_RPC::class],
                getenv('TIMEZONE'),
                $app['logger']
            );
        };

        $app[UnfurlController::class] = function ($app) {
            return new UnfurlController(
                $app[CommandResolver::class],
                getenv('SLACK_VERIFICATION_TOKEN')
            );
        };
    }
}