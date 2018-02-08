<?php

namespace Eventum\SlackUnfurl;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class UnfurlController
{
    private const COMMAND_MAP = [
        'url_verification' => Command\UrlVerification::class,
        'event_callback' => Command\EventCallback::class,
    ];

    /** @var CommandResolver */
    private $commandResolver;

    public function __construct(CommandResolver $commandResolver)
    {
        $this->commandResolver = $commandResolver;
    }

    public function __invoke(Request $request)
    {
        $content = $request->getContent();
        $payload = json_decode($content, 1);
        if (!$payload) {
            throw new InvalidArgumentException('Unable to decode json');
        }

        $type = $payload['type'] ?? null;
        $command = $this->commandResolver
            ->configure(self::COMMAND_MAP)
            ->resolve($type);

        return $command->execute($payload);
    }
}