<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Configuration;

use Sarkhanrasimoghlu\Lsim\Contracts\ConfigurationInterface;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidConfigurationException;

final readonly class LsimConfiguration implements ConfigurationInterface
{
    public function __construct(
        private string $login,
        private string $password,
        private string $sender,
        private string $baseUrl,
        private int $timeout = 30,
        private bool $verifySsl = true,
    ) {
        $this->validate();
    }

    public static function fromArray(array $config): self
    {
        $requiredKeys = ['login', 'password', 'sender'];

        foreach ($requiredKeys as $key) {
            if (!isset($config[$key]) || empty($config[$key])) {
                throw InvalidConfigurationException::missingConfiguration($key);
            }
        }

        return new self(
            login: (string) $config['login'],
            password: (string) $config['password'],
            sender: (string) $config['sender'],
            baseUrl: (string) ($config['base_url'] ?? 'https://apps.lsim.az/quicksms'),
            timeout: (int) ($config['timeout'] ?? 30),
            verifySsl: (bool) ($config['verify_ssl'] ?? true),
        );
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getVerifySsl(): bool
    {
        return $this->verifySsl;
    }

    public function validate(): bool
    {
        $this->validateCredentials();
        $this->validateUrls();
        $this->validateTimeout();

        return true;
    }

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

    private function validateUrls(): void
    {
        if (empty(trim($this->baseUrl))) {
            throw InvalidConfigurationException::missingConfiguration('base_url');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw InvalidConfigurationException::invalidUrl($this->baseUrl);
        }

        if ($this->verifySsl && !str_starts_with($this->baseUrl, 'https://')) {
            throw InvalidConfigurationException::invalidValue(
                'base_url',
                'Must use HTTPS when SSL verification is enabled'
            );
        }
    }

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
