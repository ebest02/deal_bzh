<?php

namespace Forum;

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
        
        if (!is_readable($configFile)) {
            throw new \RuntimeException(
                sprintf('Configuration file is not readable: %s', $configFile)
            );
        }
        
        $config = include $configFile;
        
        if ($config === false) {
            throw new \RuntimeException(
                sprintf('Failed to include configuration file: %s', $configFile)
            );
        }
        
        if (!is_array($config)) {
            throw new \RuntimeException(
                sprintf('Configuration file must return an array, got %s: %s', gettype($config), $configFile)
            );
        }
        
        return $config;
    }
}

