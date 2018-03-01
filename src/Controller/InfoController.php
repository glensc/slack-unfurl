<?php

namespace SlackUnfurl\Controller;

use SlackUnfurl\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InfoController
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Request $request)
    {
        $listeners = $this->dispatcher->getListeners(Events::SLACK_UNFURL);

        $content = [];
        $content[] = sprintf('Available %d Unfurlers', count($listeners));
        foreach ($listeners as $listener) {
            $content[] = sprintf('- %s', get_class($listener[0]));
        }

        $headers = ['Content-Type' => 'text/plain; charset="utf-8"'];

        return new Response(implode("\n", $content), 200, $headers);
    }
}