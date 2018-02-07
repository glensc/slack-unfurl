<?php

namespace Eventum\SlackUnfurl;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UnfurlController
{
    public function __invoke(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, 1);

        if (!$data) {
            throw new InvalidArgumentException();
        }

        if ($token = $data['token'] ?? null) {
            return $this->tokenResponse($token, $data['challenge']);
        }

        return new JsonResponse([]);
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