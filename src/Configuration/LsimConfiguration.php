<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Configuration;

use Sarkhanrasimoghlu\Lsim\Contracts\ConfigurationInterface;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidConfigurationException;

/**
 * L-sim Configuration
 * 
 * Handles L-sim SMS service configuration
 */
final readonly class LsimConfiguration implements ConfigurationInterface
{
    /**
     * Create a new configuration instance
     * 
     * @param string $login API login credentials
     * @param string $password API password
     * @param string $sender SMS sender identifier
     * @param string $baseUrl Base API URL
     * @param string $balanceUrl Balance check URL
     * @param bool $balanceCheckEnabled Whether balance checking is enabled
     * @param int $timeout HTTP timeout in seconds
     */
    public function __construct(
        private string $login,
        private string $password,
        private string $sender,
        private string $baseUrl,
        private string $balanceUrl,
        private bool $balanceCheckEnabled = false,
        private int $timeout = 30
    ) {
        $this->validate();
    }

    /**
     * Create configuration from array
     * 
     * @param array<string, mixed> $config Configuration array
     * @return self
     * @throws InvalidConfigurationException
     */
    public static function fromArray(array $config): self
    {
        $requiredKeys = ['login', 'password', 'sender', 'base_url'];
        
        foreach ($requiredKeys as $key) {
            if (!isset($config[$key]) || empty($config[$key])) {
                throw InvalidConfigurationException::missingConfiguration($key);
            }
        }

        return new self(
            login: (string) $config['login'],
            password: (string) $config['password'],
            sender: (string) $config['sender'],
            baseUrl: (string) $config['base_url'],
            balanceUrl: (string) ($config['balance_url'] ?? ''),
            balanceCheckEnabled: (bool) ($config['balance_check_enabled'] ?? false),
            timeout: (int) ($config['timeout'] ?? 30)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * {@inheritDoc}
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getBalanceUrl(): string
    {
        return $this->balanceUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function isBalanceCheckEnabled(): bool
    {
        return $this->balanceCheckEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(): bool
    {
        $this->validateCredentials();
        $this->validateUrls();
        $this->validateTimeout();
        
        return true;
    }

    /**
     * Validate credentials
     * 
     * @throws InvalidConfigurationException
     */
    private function validateCredentials(): void
    {
        if (empty(trim($this->login))) {
            throw InvalidConfigurationException::missingConfiguration('login');
        }

        if (empty(trim($this->password))) {
            throw InvalidConfigurationException::missingConfiguration('password');
        }

        if (empty(trim($this->sender))) {
            throw InvalidConfigurationException::missingConfiguration('sender');
        }
    }

    /**
     * Validate URLs
     * 
     * @throws InvalidConfigurationException
     */
    private function validateUrls(): void
    {
        if (empty(trim($this->baseUrl))) {
            throw InvalidConfigurationException::missingConfiguration('base_url');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw InvalidConfigurationException::invalidUrl($this->baseUrl);
        }

        // Enforce HTTPS for security
        if (!str_starts_with($this->baseUrl, 'https://')) {
            throw InvalidConfigurationException::invalidValue(
                'base_url',
                'Must use HTTPS for security'
            );
        }

        if ($this->balanceCheckEnabled) {
            if (empty(trim($this->balanceUrl))) {
                throw InvalidConfigurationException::missingConfiguration('balance_url');
            }

            if (!filter_var($this->balanceUrl, FILTER_VALIDATE_URL)) {
                throw InvalidConfigurationException::invalidUrl($this->balanceUrl);
            }

            if (!str_starts_with($this->balanceUrl, 'https://')) {
                throw InvalidConfigurationException::invalidValue(
                    'balance_url',
                    'Must use HTTPS for security'
                );
            }
        }
    }

    /**
     * Validate timeout
     * 
     * @throws InvalidConfigurationException
     */
    private function validateTimeout(): void
    {
        if ($this->timeout < 1 || $this->timeout > 300) {
            throw InvalidConfigurationException::invalidValue(
                'timeout',
                'Must be between 1 and 300 seconds'
            );
        }
    }
}