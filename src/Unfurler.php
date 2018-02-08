<?php

namespace Eventum\SlackUnfurl;

use DateTime;
use DateTimeZone;
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
    /** @var DateTimeZone */
    private $utc;
    /** @var DateTimeZone */
    private $timeZone;

    public function __construct(
        Eventum_RPC $apiClient,
        string $timeZone,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->apiClient = $apiClient;
        $this->utc = new DateTimeZone('UTC');
        $this->timeZone = new DateTimeZone($timeZone);
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
                [
                    'title' => 'Last update',
                    'value' => $this->getLastUpdate($issue)->format('Y-m-d H:i:s'),
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

    /**
     * Get last update whether internal or public last action date
     *
     * @param array $issue
     * @return DateTime last action date in specified timeZone
     */
    private function getLastUpdate(array $issue)
    {
        $ts1 = new DateTime($issue['iss_last_internal_action_date'], $this->utc);
        $ts2 = new DateTime($issue['iss_last_public_action_date'], $this->utc);

        $lastUpdated = $ts1 > $ts2 ? $ts1 : $ts2;
        $lastUpdated->setTimezone($this->timeZone);

        return $lastUpdated;
    }
}
