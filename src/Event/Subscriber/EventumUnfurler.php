<?php

namespace Eventum\SlackUnfurl\Event\Subscriber;

use Eventum\SlackUnfurl\Event\Events;
use Eventum\SlackUnfurl\Event\UnfurlEvent;
use Eventum\SlackUnfurl\LoggerTrait;
use Eventum\SlackUnfurl\Unfurler;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventumUnfurler implements EventSubscriberInterface
{
    use LoggerTrait;

    /** @var string */
    private $domain;
    /** @var Unfurler */
    private $unfurler;

    public function __construct(
        Unfurler $unfurler,
        string $domain,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->domain = $domain;
        $this->unfurler = $unfurler;

        if (!$this->domain) {
            throw new InvalidArgumentException('Domain not set');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::SLACK_UNFURL => ['unfurl', 10],
        ];
    }

    public function unfurl(UnfurlEvent $event)
    {
        foreach ($this->getMatchingLinks($event) as $link) {
            $issueId = $this->getIssueId($link);
            if (!$issueId) {
                $this->error('Could not extract issueId', ['link' => $link]);
                continue;
            }

            $url = $link['url'];
            $unfurl = $this->unfurler->unfurl($issueId, $url);
            $event->addUnfurl($url, $unfurl);
        }
    }

    private function getMatchingLinks(UnfurlEvent $event)
    {
        foreach ($event->getLinks() as $link) {
            $domain = $link['domain'] ?? null;
            if ($domain !== $this->domain) {
                continue;
            }

            yield $link;
        }
    }

    private function getIssueId($link)
    {
        if (!preg_match('#view.php\?id=(?P<id>\d+)#', $link['url'], $m)) {
            return null;
        }

        return (int)$m['id'];
    }
}