<?php


namespace Eventum\SlackUnfurl\Command;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class UrlVerification implements CommandInterface
{
    public function execute(array $payload): JsonResponse
    {
        $token = $payload['token'] ?? null;
        $challenge = $payload['challenge'] ?? null;
        if ($token && $challenge) {
            return $this->tokenResponse($token, $challenge);
        }

        throw new InvalidArgumentException();
    }

    /**
     * Handle Request URL Configuration & Verification
     *
     * @param string $token
     * @param string $challenge
     * @return JsonResponse
     * @see https://api.slack.com/events-api#request_url_configuration__amp__verification
     */
    private function tokenResponse(string $token, string $challenge)
    {
        if ($token === getenv('SLACK_VERIFICATION_TOKEN')) {
            return new JsonResponse(['challenge' => $challenge]);
        }

        throw new InvalidArgumentException();
    }
}