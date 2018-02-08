<?php

namespace Eventum\SlackUnfurl;

use Eventum_RPC;
use Psr\Log\LoggerInterface;

class Unfurler
{
    use LoggerTrait;

    /** @var Eventum_RPC */
    private $apiClient;

    public function __construct(
        Eventum_RPC $apiClient,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->apiClient = $apiClient;
    }

    public function unfurl(int $issueId)
    {
        $issue = $this->apiClient->getSimpleIssueDetails($issueId);
        $this->debug('issue', ['issue' => $issue]);

        return [
            'text' => "Issue #{$issueId}: {$issue['summary']}",
        ];
    }
}