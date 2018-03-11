<?php

namespace SlackUnfurl\Command;

use SlackUnfurl\RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class UrlVerification implements CommandInterface
{
    /**
     * Handle Request URL Configuration & Verification
     *
     * @param array $payload
     * @return JsonResponse
     * @see https://api.slack.com/events-api#request_url_configuration__amp__verification
     */
    public function execute(array $payload): JsonResponse
    {
        $challenge = $payload['challenge'] ?? null;
        if ($challenge) {
            return new JsonResponse(['challenge' => $challenge]);
        }

        throw new RuntimeException();
    }
}