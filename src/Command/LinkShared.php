<?php

namespace Eventum\SlackUnfurl\Command;

use Eventum\SlackUnfurl\LoggerTrait;
use Eventum\SlackUnfurl\SlackClient;
use Eventum\SlackUnfurl\Unfurler;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkShared implements CommandInterface
{
    use LoggerTrait;

    /** @var string */
    private $matchDomain;
    /** @var SlackClient */
    private $slackClient;
    /** @var Unfurler */
    private $unfurler;

    public function __construct(
        Unfurler $unfurler,
        SlackClient $slackClient,
        string $matchDomain,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->matchDomain = $matchDomain;
        $this->unfurler = $unfurler;
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

            $url = $link['url'];
            $unfurls[$url] = $this->unfurler->unfurl($issueId, $url);
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
            if ($domain !== $this->matchDomain) {
                continue;
            }

            yield $link;
        }
    }
}