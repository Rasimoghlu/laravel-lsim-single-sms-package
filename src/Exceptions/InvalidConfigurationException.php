<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Exceptions;

/**
 * Invalid Configuration Exception
 * 
 * Thrown when configuration is invalid or incomplete
 */
final class InvalidConfigurationException extends SmsException
{
    /**
     * Create exception for missing configuration
     * 
     * @param string $configKey The missing configuration key
     * @return self
     */
    public static function missingConfiguration(string $configKey): self
    {
        return new self("Missing required configuration: {$configKey}");
    }

    /**
     * Create exception for invalid configuration value
     * 
     * @param string $configKey The configuration key
     * @param string $reason The reason why it's invalid
     * @return self
     */
    public static function invalidValue(string $configKey, string $reason): self
    {
        return new self("Invalid configuration value for {$configKey}: {$reason}");
    }

    /**
     * Create exception for invalid URL
     * 
     * @param string $url The invalid URL
     * @return self
     */
    public static function invalidUrl(string $url): self
    {
        return new self("Invalid URL configuration: {$url}");
    }
}