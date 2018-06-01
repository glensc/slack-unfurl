<?php

namespace SlackUnfurl;

use Psr\Log\LoggerInterface;
use Silex\Application as BaseApplication;
use Silex\Provider\MonologServiceProvider;
use SlackUnfurl\Controller\InfoController;
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

        $this->setupErrorHandler();
        $this->registerProviders();
        $this->configureRoutes();
    }

    private function registerProviders()
    {
        $this->register(new ServiceProvider\ServiceProvider());
        $this->register(new ServiceProvider\CommandProvider());
        $this->register(new MonologServiceProvider(), [
            'monolog.name' => self::NAME,
            'monolog.logfile' => "{$this['appDir']}/var/log/{$this['env']}.log",
            'monolog.use_error_handler' => true,
        ]);
    }

    private function configureRoutes()
    {
        $this->post($this['unfurl.app_prefix'], function (Request $request) {
            return $this[UnfurlController::class]($request);
        });
        $this->get($this['unfurl.app_prefix'], function (Request $request) {
            return $this[InfoController::class]($request);
        });
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