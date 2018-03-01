<?php

namespace SlackUnfurl;

use Psr\Log\LoggerInterface;
use Silex\Application as BaseApplication;
use Silex\Provider\MonologServiceProvider;
use SlackUnfurl\Controller\UnfurlController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class Application extends BaseApplication
{
    const NAME = 'unfurl';

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->registerProviders();
        $this->configureRoutes();
        $this->setupErrorHandler();
    }

    private function registerProviders()
    {
        $this->register(new ServiceProvider\ServiceProvider());
        $this->register(new ServiceProvider\CommandProvider());
        $this->register(new MonologServiceProvider(), [
            'monolog.name' => self::NAME,
            'monolog.logfile' => dirname(__DIR__) . "/var/logs/{$this['env']}.log",
            'monolog.use_error_handler' => true,
        ]);
    }

    private function configureRoutes()
    {
        $this->post('/', $this[UnfurlController::class]);
    }

    private function setupErrorHandler()
    {
        $this->error(function (Throwable $e, Request $request, $code) {
            /** @var LoggerInterface $logger */
            $logger = $this['logger'];
            $logger->error($e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return new JsonResponse('Internal Error', $code);
        });
    }
}