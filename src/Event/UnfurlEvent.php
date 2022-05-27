<?php

namespace SlackUnfurl\Event;

use Generator;
use Symfony\Contracts\EventDispatcher\Event;

class UnfurlEvent extends Event
{

    protected array $unfurls = [];

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
    public function __construct(
      protected array $data = []
    ) {}

    public function getData(): array
    {
        return $this->data;
    }

    public function getLinks(): array
    {
        return $this->data['links'] ?? [];
    }

    public function getMatchingLinks(string $domain): Generator
    {
        foreach ($this->getLinks() as $link) {
            if ($link['domain'] !== $domain) {
                continue;
            }

            yield $link;
        }
    }

    public function getUnfurls(): array
    {
        return $this->unfurls;
    }

    public function addUnfurl(string $url, array $unfurl): self
    {
        $this->unfurls[$url] = $unfurl;

        return $this;
    }
}
