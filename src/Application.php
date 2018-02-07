<?php

namespace Eventum\SlackUnfurl;

use Eventum\SlackUnfurl\ServiceProvider\CommandProvider;
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
        $this->register(new CommandProvider());
    }

    private function configureRoutes()
    {
        $this->post('/', UnfurlController::class);
    }
}