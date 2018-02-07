<?php

namespace Eventum\SlackUnfurl\Command;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventCallback implements CommandInterface
{
    /** @var LinkShared */
    private $linkShared;

    public function __construct($linkShared)
    {
        $this->linkShared = $linkShared;
    }

    public function execute(array $payload): JsonResponse
    {
        $event = $payload['event'] ?? null;
        if (!$event) {
            throw new InvalidArgumentException();
        }

        $type = $event['type'] ?? null;

        $command = $this->getCommand($type);
        return $command->execute($event);
    }

    /**
     * @param string $type
     * @return CommandInterface
     */
    private function getCommand(string $type)
    {
        $map = [
            'link_shared' => $this->linkShared,
        ];

        $command = $map[$type] ?? null;
        if (!$command) {
            throw new InvalidArgumentException();
        }

        return $command;
    }
}