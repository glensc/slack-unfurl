<?php

namespace Eventum\SlackUnfurl\Command;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkShared implements CommandInterface
{
    /** @var string */
    private $domain;

    public function __construct()
    {
        $this->domain = getenv('EVENTUM_DOMAIN');

        if (!$this->domain) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Handle Link Shared event
     * @param array $event
     * @return JsonResponse
     * @see https://api.slack.com/events/link_shared
     */
    public function execute(array $event): JsonResponse
    {
        $links = $event['links'] ?? null;
        foreach ($this->getMatchingLinks($links) as $link) {
        }

        return new JsonResponse([]);
    }

    public function getMatchingLinks(array $links)
    {
        foreach ($links as $link) {
            $domain = $link['domain'] ?? null;
            if ($domain != $this->domain) {
                continue;
            }

            yield $link;
        }
    }
}