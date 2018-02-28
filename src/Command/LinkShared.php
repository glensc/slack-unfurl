<?php

namespace Eventum\SlackUnfurl\Command;

use Eventum\SlackUnfurl\Event\Events;
use Eventum\SlackUnfurl\Event\UnfurlEvent;
use Eventum\SlackUnfurl\LoggerTrait;
use Eventum\SlackUnfurl\SlackClient;
use Psr\Log\LoggerInterface;
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
        /** @var UnfurlEvent $event */
        $event = $this->dispatcher->dispatch(Events::SLACK_UNFURL, new UnfurlEvent($data));
        $unfurls = $event->getUnfurls();

        $this->debug('unfurls', ['channel' => $data['channel'], 'ts' => $data['message_ts'], 'unfurls' => $unfurls]);
        $this->slackClient->unfurl($data['channel'], $data['message_ts'], $unfurls);

        return new JsonResponse([]);
    }
}