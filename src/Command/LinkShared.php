<?php

namespace Eventum\SlackUnfurl\Command;

use Eventum_RPC;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkShared implements CommandInterface
{
    /** @var string */
    private $domain;
    /** @var Eventum_RPC */
    private $apiClient;

    public function __construct(Eventum_RPC $apiClient)
    {
        $this->domain = getenv('EVENTUM_DOMAIN');
        $this->apiClient = $apiClient;

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
        foreach ($this->getIssueIds($links) as $issueId) {
            $issue = $this->apiClient->getSimpleIssueDetails($issueId);
        }

        return new JsonResponse([]);
    }

    private function getIssueIds(array $links)
    {
        foreach ($this->getMatchingLinks($links) as $link) {
            if (!preg_match('#view.php\?id=(?P<id>\d+)#', $link['url'], $m)) {
                continue;
            }

            yield (int)$m['id'];
        }
    }

    private function getMatchingLinks(array $links)
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