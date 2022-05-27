<?php

namespace SlackUnfurl\Controller;

use SlackUnfurl\Command\LinkShared;
use SlackUnfurl\Command\UrlVerification;
use SlackUnfurl\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UnfurlController extends AbstractController
{

    public function __construct(
      private readonly UrlVerification $verification,
      private readonly LinkShared      $linkShared) {}


    #[Route('/', name: 'unfurl', methods: 'POST')]
    public function unfurl(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $payload = json_decode($content, 1, 512, JSON_THROW_ON_ERROR);
        if (!$payload) {
            throw new RuntimeException('Unable to decode json');
        }

        $this->verifyToken($payload['token'] ?? null);

        if ($payload['type'] === 'url_verification') {
            return $this->verification->execute($payload);
        }
        if ($payload['type'] === 'event_callback') {
            return $this->linkShared->execute($payload);
        }
        throw new RuntimeException(sprintf('Unable to resolve %s', $payload['type']));
    }

    /**
     * @param string $token
     * @throws RuntimeException
     */
    private function verifyToken(string $token): void
    {
        if ($token !== $this->getParameter('unfurl.slack_verification_token')) {
            throw new RuntimeException('Token verification failed');
        }
    }
}
