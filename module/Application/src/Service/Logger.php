<?php

namespace Application\Service;

class Logger
{
    protected $logFile;
    protected $errorLogFile;

    public function __construct(?string $logFile = null, ?string $errorLogFile = null)
    {
        $baseDir = dirname(dirname(dirname(dirname(__DIR__))));
        $this->logFile = $logFile ?? $baseDir . '/logs/app.log';
        $this->errorLogFile = $errorLogFile ?? $baseDir . '/logs/error.log';
        
        // Créer le répertoire logs s'il n'existe pas
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public function log(string $message, string $level = 'INFO', array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = sprintf(
            "[%s] [%s] %s%s\n",
            $timestamp,
            $level,
            $message,
            $contextStr
        );
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log($message, 'DEBUG', $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log($message, 'INFO', $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log($message, 'WARNING', $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log($message, 'ERROR', $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log($message, 'CRITICAL', $context);
    }

    public function logException(\Throwable $exception, string $level = 'ERROR'): void
    {
        $message = sprintf(
            'Exception: %s in %s:%d - %s',
            get_class($exception),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage()
        );
        
        $this->log($message, $level, [
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}

