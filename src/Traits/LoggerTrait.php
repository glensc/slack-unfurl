<?php

namespace SlackUnfurl\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait as PsrLoggerTrait;

trait LoggerTrait
{
    use PsrLoggerTrait;

    /** @var LoggerInterface */
    protected $logger;

    public function log($level, $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}