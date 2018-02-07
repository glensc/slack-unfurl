<?php

namespace Eventum\SlackUnfurl;

use Silex\Application as BaseApplication;
use Symfony\Component\HttpFoundation\JsonResponse;

class Application extends BaseApplication
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->configureRoutes();
    }

    private function configureRoutes()
    {
        $this->get('/', function () {
            return new JsonResponse([]);
        })->bind('homepage');
    }
}