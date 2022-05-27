<?php

namespace SlackUnfurl;

use SlackUnfurl\Traits\SlackEscapeTrait;
use wrapi\slack\slack;

class SlackClient
{
    use SlackEscapeTrait;

    private slack $slack;

    public function __construct(string $apiToken)
    {
        $this->slack = new slack($apiToken);
    }

    public function unfurl(string $channel, string $ts, array $unfurls): void
    {
        $queryString = [
            'ts' => $ts,
            'channel' => $channel,
            'unfurls' => json_encode($unfurls, JSON_THROW_ON_ERROR),
        ];

        $response = $this->slack->chat->unfurl($queryString);
        if ($response['ok'] !== true) {
            throw new RuntimeException($response['error']);
        }
    }
}
