<?php

namespace SlackUnfurl\Test;

use SlackUnfurl\Event\UnfurlEvent;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function createUnfurlEvent(string $url): UnfurlEvent
    {
        $domain = parse_url($url, PHP_URL_HOST);
        $data = [
            'type' => 'link_shared',
            'user' => 'Uxxxxxxxx',
            'channel' => 'Dxxxxxxxx',
            'message_ts' => '1518125979.000251',
            'links' => [
                [
                    'url' => $url,
                    'domain' => $domain,
                ],
            ],
        ];

        return new UnfurlEvent($data);
    }
}
