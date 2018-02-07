<?php

namespace Eventum\SlackUnfurl;

use Silex\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->configureRoutes();
    }

    private function configureRoutes()
    {
        $this->post('/', UnfurlController::class);
    }
}