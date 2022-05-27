<?php

namespace SlackUnfurl\Controller;

use SlackUnfurl\Event\Events;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{

    public function __construct(
      private readonly EventDispatcherInterface $dispatcher
    ) {}

    #[Route('/', name: 'info', methods: 'GET')]
    public function info(): Response
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
