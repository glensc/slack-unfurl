<?php

namespace Eventum\SlackUnfurl\Command;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use function var_export;

class EventCallback implements CommandInterface
{
    public function execute(array $payload): JsonResponse
    {
        $event = $payload['event'] ?? null;
        if (!$event) {
            throw new InvalidArgumentException();
        }
    }
}