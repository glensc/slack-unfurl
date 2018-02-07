<?php

namespace Eventum\SlackUnfurl;

use Eventum\SlackUnfurl\ServiceProvider;
use Silex\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->registerProviders();
        $this->configureRoutes();
    }

    private function registerProviders()
    {
        $this->register(new ServiceProvider\CommandProvider());
        $this->register(new ServiceProvider\ServiceProvider());
    }

    private function configureRoutes()
    {
        $this->post('/', UnfurlController::class);
    }
}