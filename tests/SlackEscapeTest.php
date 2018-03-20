<?php

namespace SlackUnfurl\Test;

use SlackUnfurl\SlackClient;

class SlackEscapeTest extends TestCase
{
    /** @var SlackClient */
    private $client;

    public function setUp()
    {
        $this->client = new SlackClient('boo');
    }

    /**
     * @dataProvider escapeData
     */
    public function testEscape(string $text, string $expected)
    {
        $result = $this->client->escape($text);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider urlEncodeData
     */
    public function testUrlEncode(string $text, string $expected)
    {
        $result = $this->client->urlencode($text);
        $this->assertEquals($expected, $result);
    }

    public function escapeData()
    {
        return [
            'simple test' => [
                '*<http://her|e|link not b$a-|bald>*',
                '*&lt;http://her|e|link not b$a-|bald&gt;*',
            ],
        ];
    }

    public function urlEncodeData()
    {
        return [
            'simple test' => [
                'http://her|<e>|&',
                'http://her%7C%3Ce%3E%7C%26',
            ],
        ];
    }
}