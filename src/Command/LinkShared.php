<?php

namespace SlackUnfurl\Command;

use Psr\Log\LoggerInterface;
use SlackUnfurl\Event\Events;
use SlackUnfurl\Event\UnfurlEvent;
use SlackUnfurl\Traits\LoggerTrait;
use SlackUnfurl\SlackClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkShared implements CommandInterface
{
    use LoggerTrait;

    /** @var SlackClient */
    private $slackClient;
    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        SlackClient $slackClient,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->dispatcher = $dispatcher;
        $this->slackClient = $slackClient;
        $this->logger = $logger;
    }

    /**
     * Handle Link Shared event
     * @param array $data
     * @return JsonResponse
     * @see https://api.slack.com/events/link_shared
     */
    public function execute(array $data): JsonResponse
    {
        $this->debug('link_shared', ['event' => $data]);

        /** @var UnfurlEvent $event */
        $event = $this->dispatcher->dispatch(Events::SLACK_UNFURL, new UnfurlEvent($data));
        $unfurls = $event->getUnfurls();

        $this->debug('unfurls', ['channel' => $data['channel'], 'ts' => $data['message_ts'], 'unfurls' => $unfurls]);
        if ($unfurls) {
            $this->slackClient->unfurl($data['channel'], $data['message_ts'], $unfurls);
        }

        return new JsonResponse([]);
    }
}