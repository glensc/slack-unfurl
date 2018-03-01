<?php

namespace SlackUnfurl\ServiceProvider;

use Eventum_RPC;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SlackUnfurl\CommandResolver;
use SlackUnfurl\Controller\UnfurlController;
use SlackUnfurl\Event\Subscriber\EventumUnfurler;
use SlackUnfurl\SlackClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

        $app[EventumUnfurler::class] = function ($app) {
            return new EventumUnfurler(
                $app[Eventum_RPC::class],
                getenv('EVENTUM_DOMAIN'),
                getenv('TIMEZONE'),
                $app['logger']
            );
        };

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
            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $app['dispatcher'];
            $dispatcher->addSubscriber($app[EventumUnfurler::class]);

            return $dispatcher;
        };
    }
}