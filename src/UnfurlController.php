<?php

namespace Eventum\SlackUnfurl;

use Symfony\Component\HttpFoundation\JsonResponse;

class UnfurlController
{
    public function __invoke()
    {
        return new JsonResponse([]);
    }
}