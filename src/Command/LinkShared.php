<?php

namespace Eventum\SlackUnfurl\Command;

use Eventum\SlackUnfurl\LoggerTrait;
use Eventum\SlackUnfurl\SlackClient;
use Eventum_RPC;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkShared implements CommandInterface
{
    use LoggerTrait;

    /** @var string */
    private $matchDomain;
    /** @var Eventum_RPC */
    private $apiClient;
    /** @var SlackClient */
    private $slackClient;

    public function __construct(
        Eventum_RPC $apiClient,
        SlackClient $slackClient,
        string $matchDomain,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->matchDomain = $matchDomain;
        $this->apiClient = $apiClient;
        $this->slackClient = $slackClient;

        if (!$this->matchDomain) {
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

        $unfurls = [];
        foreach ($this->getMatchingLinks($links) as $link) {
            $issueId = $this->getIssueId($link);
            if (!$issueId) {
                $this->error('Could not extract issueId', ['link' => $link]);
                continue;
            }

            $issue = $this->apiClient->getSimpleIssueDetails($issueId);
            $this->debug('issue', ['issue' => $issue]);

            $url = $link['url'];
            $unfurls[$url] = [
                'text' => "Issue #{$issueId}: {$issue['summary']}",
            ];
        }

        $this->debug('unfurls', ['channel' => $event['channel'], 'ts' => $event['message_ts'], 'unfurls' => $unfurls]);
        $this->slackClient->unfurl($event['channel'], $event['message_ts'], $unfurls);

        return new JsonResponse([]);
    }

    private function getIssueId($link)
    {
        if (!preg_match('#view.php\?id=(?P<id>\d+)#', $link['url'], $m)) {
            return null;
        }

        return (int)$m['id'];
    }

    private function getMatchingLinks(array $links)
    {
        foreach ($links as $link) {
            $domain = $link['domain'] ?? null;
            if ($domain != $this->matchDomain) {
                continue;
            }

            yield $link;
        }
    }
}