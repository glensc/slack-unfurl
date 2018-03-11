<?php

namespace SlackUnfurl\Command;

use SlackUnfurl\CommandResolver;
use SlackUnfurl\RuntimeException;
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
            throw new RuntimeException('Required event missing from payload');
        }

        $command = $this->commandResolver
            ->configure(self::COMMAND_MAP)
            ->resolve($event['type'] ?? null);

        return $command->execute($event);
    }
}