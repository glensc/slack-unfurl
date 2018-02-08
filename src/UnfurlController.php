<?php

namespace Eventum\SlackUnfurl;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class UnfurlController
{
    public function __invoke(Request $request, Application $app)
    {
        $content = $request->getContent();
        $payload = json_decode($content, 1);
        if (!$payload) {
            throw new InvalidArgumentException();
        }

        $type = $payload['type'] ?? null;
        $command = $this->getCommand($app, $type);

        return $command->execute($payload);
    }

    /**
     * @param Application $app
     * @param string $type
     * @return Command\CommandInterface
     */
    private function getCommand(Application $app, string $type)
    {
        $map = [
            'url_verification' => Command\UrlVerification::class,
            'event_callback' => Command\EventCallback::class,
        ];

        $className = $map[$type] ?? null;
        if (!$className) {
            throw new InvalidArgumentException();
        }

        return $app[$className];
    }
}