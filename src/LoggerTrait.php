<?php

namespace Eventum\SlackUnfurl;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait as PsrLoggerTrait;

trait LoggerTrait
{
    use PsrLoggerTrait;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }
}