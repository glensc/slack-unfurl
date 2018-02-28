<?php

namespace Eventum\SlackUnfurl\Event;

use Symfony\Component\EventDispatcher\Event;

class UnfurlEvent extends Event
{
    /** @var array */
    protected $data;

    /** @var array */
    protected $unfurls = [];

    /**
     * @param array $data "event" key from payload
     * {
     *   "type": "link_shared",
     *   "user": "Uxxxxxxxx",
     *   "channel": "Dxxxxxxxx",
     *   "message_ts": "1518125979.000251",
     *   "links": [
     *     {
     *       "url": "https://eventum.example.net/view.php?id=98363",
     *       "domain": "eventum.example.net"
     *     }
     *   ]
     *  }
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getLinks(): array
    {
        return $this->data['links'] ?? [];
    }

    public function getUnfurls(): array
    {
        return $this->unfurls;
    }

    public function addUnfurl(string $url, array $unfurl)
    {
        $this->unfurls[$url] = $unfurl;

        return $this;
    }
}