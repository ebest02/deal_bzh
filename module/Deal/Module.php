<?php

namespace Deal;

class Module
{
    public function getConfig(): array
    {
        $configFile = __DIR__ . '/config/module.config.php';
        if (!file_exists($configFile)) {
            throw new \RuntimeException(
                sprintf('Configuration file not found: %s', $configFile)
            );
        }
        $config = include $configFile;
        if (!is_array($config)) {
            throw new \RuntimeException(
                sprintf('Configuration file must return an array: %s', $configFile)
            );
        }
        return $config;
    }
}

