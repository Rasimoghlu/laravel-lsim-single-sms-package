<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Contracts;

/**
 * Configuration Interface
 * 
 * Contract for configuration management
 */
interface ConfigurationInterface
{
    /**
     * Get login credentials
     * 
     * @return string The API login
     */
    public function getLogin(): string;

    /**
     * Get password
     * 
     * @return string The API password
     */
    public function getPassword(): string;

    /**
     * Get sender name
     * 
     * @return string The sender identifier
     */
    public function getSender(): string;

    /**
     * Get base URL
     * 
     * @return string The base API URL
     */
    public function getBaseUrl(): string;

    /**
     * Get balance URL
     * 
     * @return string The balance check URL
     */
    public function getBalanceUrl(): string;

    /**
     * Check if balance checking is enabled
     * 
     * @return bool True if balance checking is enabled
     */
    public function isBalanceCheckEnabled(): bool;

    /**
     * Get HTTP timeout in seconds
     * 
     * @return int Timeout in seconds
     */
    public function getTimeout(): int;

    /**
     * Validate configuration completeness
     * 
     * @return bool True if configuration is valid
     * @throws \Sarkhanrasimoghlu\Lsim\Exceptions\InvalidConfigurationException
     */
    public function validate(): bool;
}