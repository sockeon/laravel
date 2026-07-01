<?php

namespace Sockeon\Laravel\Logging;

use Psr\Log\LoggerInterface as PsrLogger;
use Sockeon\Sockeon\Contracts\LoggerInterface;
use Sockeon\Sockeon\Logging\LogLevel;
use Throwable;

class LaravelLogger implements LoggerInterface
{
    public function __construct(private PsrLogger $logger) {}

    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function exception(Throwable $exception, array $context = [], string $level = LogLevel::ERROR): void
    {
        $this->logger->log($level, $exception->getMessage(), array_merge($context, ['exception' => $exception]));
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
