<?php

namespace SlackUnfurl;

use InvalidArgumentException;
use wrapi\slack\slack;

class SlackClient
{
    /** @var slack */
    private $slack;

    public function __construct(string $apiToken)
    {
        $this->slack = new slack($apiToken);
    }

    public function unfurl(string $channel, string $ts, array $unfurls)
    {
        $queryString = [
            'ts' => $ts,
            'channel' => $channel,
            'unfurls' => json_encode($unfurls),
        ];

        $response = $this->slack->chat->unfurl($queryString);
        if ($response['ok'] !== true) {
            throw new InvalidArgumentException($response['error']);
        }
    }
}