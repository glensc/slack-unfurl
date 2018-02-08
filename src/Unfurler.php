<?php

namespace Eventum\SlackUnfurl;

use Eventum_RPC;
use Psr\Log\LoggerInterface;

class Unfurler
{
    use LoggerTrait;

    /**
     * getDetails keys to retrieve
     * @see getIssueDetails
     */
    private const MATCH_KEYS = [
        'assignments',
        'iss_created_date',
        'iss_created_date_ts',
        'iss_description',
        'iss_id',
        'iss_last_internal_action_date',
        'iss_last_public_action_date',
        'iss_original_description',
        'iss_summary',
        'iss_updated_date',
        'prc_title',
        'pri_title',
        'reporter',
        'sta_title',
    ];

    /** @var Eventum_RPC */
    private $apiClient;

    public function __construct(
        Eventum_RPC $apiClient,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->apiClient = $apiClient;
    }

    public function unfurl(int $issueId, string $url)
    {
        $issue = $this->getIssueDetails($issueId);
        $this->debug('issue', ['issue' => $issue]);

        return [
            'title' => "{$issue['prc_title']} <$url|Issue #{$issueId}> : {$issue['iss_summary']}",
            'color' => '#006486',
            'footer' => "Created by {$issue['reporter']}",
            'fields' => [
                [
                    'title' => 'Reported by',
                    'value' => $issue['reporter'],
                    'short' => true,
                ],
                [
                    'title' => 'Priority',
                    'value' => $issue['pri_title'],
                    'short' => true,
                ],
                [
                    'title' => 'Assignment',
                    'value' => $issue['assignments'],
                    'short' => true,
                ],
                [
                    'title' => 'Status',
                    'value' => $issue['sta_title'],
                    'short' => true,
                ],
            ],
        ];
    }

    /**
     * Get issue details, but filter only needed keys.
     *
     * @param int $issueId
     * @return array
     */
    private function getIssueDetails(int $issueId)
    {
        $issue = $this->apiClient->getIssueDetails($issueId);

        return array_intersect_key($issue, array_flip(self::MATCH_KEYS));
    }
}
