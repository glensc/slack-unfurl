<?php

namespace Eventum\SlackUnfurl\Command;

use Eventum\SlackUnfurl\CommandResolver;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventCallback implements CommandInterface
{
    private const COMMAND_MAP = [
        'link_shared' => LinkShared::class,
    ];

    /** @var CommandResolver */
    private $commandResolver;

    public function __construct(CommandResolver $commandResolver)
    {
        $this->commandResolver = $commandResolver;
    }

    public function execute(array $payload): JsonResponse
    {
        $event = $payload['event'] ?? null;
        if (!$event) {
            throw new InvalidArgumentException();
        }

        $type = $event['type'] ?? null;
        $command = $this->commandResolver
            ->configure(self::COMMAND_MAP)
            ->resolve($type);

        return $command->execute($event);
    }
}