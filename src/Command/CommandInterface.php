<?php

namespace SlackUnfurl\Command;

use Symfony\Component\HttpFoundation\JsonResponse;

interface CommandInterface
{
    public function execute(array $payload): JsonResponse;
}