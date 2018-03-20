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

    /**
     * Escape text string.
     * For url use urlencode() as url needs '|'-separator being encoded differently.
     *
     * @param string $text
     * @return string
     * @see https://api.slack.com/docs/message-formatting#how_to_escape_characters
     */
    public function escape(string $text): string
    {
        $replacements = [
            '&' => '&amp;',
            '<' => '&lt;',
            '>' => '&gt;',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Escape url to be safe to be inserted into <URL|text> block.
     * NOTE: does not handle double escaping (%xx already encoded).
     *
     * @param string $url
     * @return string
     * @see https://api.slack.com/docs/message-formatting#how_to_escape_characters
     */
    public function urlencode(string $url): string
    {
        $replacements = [
            '%' => '%25',
            '&' => '%26',
            '<' => '%3C',
            '>' => '%3E',
            '|' => '%7C',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $url);
    }
}