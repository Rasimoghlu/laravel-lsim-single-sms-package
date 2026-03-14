<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\Configuration\LsimConfiguration;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidConfigurationException;

class LsimConfigurationTest extends TestCase
{
    private function validConfig(array $overrides = []): array
    {
        return array_merge([
            'login'      => 'test_login',
            'password'   => 'test_password',
            'sender'     => 'TestSender',
            'base_url'   => 'https://apps.lsim.az/quicksms',
            'timeout'    => 30,
            'verify_ssl' => true,
        ], $overrides);
    }

    public function test_creates_from_valid_array(): void
    {
        $config = LsimConfiguration::fromArray($this->validConfig());

        $this->assertSame('test_login', $config->getLogin());
        $this->assertSame('test_password', $config->getPassword());
        $this->assertSame('TestSender', $config->getSender());
        $this->assertSame('https://apps.lsim.az/quicksms', $config->getBaseUrl());
        $this->assertSame(30, $config->getTimeout());
        $this->assertTrue($config->getVerifySsl());
    }

    public function test_uses_default_base_url(): void
    {
        $config = LsimConfiguration::fromArray($this->validConfig(['base_url' => null]));

        $this->assertSame('https://apps.lsim.az/quicksms', $config->getBaseUrl());
    }

    public function test_throws_exception_for_missing_login(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('login');

        LsimConfiguration::fromArray($this->validConfig(['login' => null]));
    }

    public function test_throws_exception_for_missing_password(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('password');

        LsimConfiguration::fromArray($this->validConfig(['password' => null]));
    }

    public function test_throws_exception_for_missing_sender(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('sender');

        LsimConfiguration::fromArray($this->validConfig(['sender' => null]));
    }

    public function test_throws_exception_for_empty_login(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        LsimConfiguration::fromArray($this->validConfig(['login' => '']));
    }

    public function test_throws_exception_for_invalid_url(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        LsimConfiguration::fromArray($this->validConfig(['base_url' => 'not-a-url']));
    }

    public function test_throws_exception_for_http_url_when_ssl_enabled(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('HTTPS');

        LsimConfiguration::fromArray($this->validConfig([
            'base_url'   => 'http://apps.lsim.az/quicksms',
            'verify_ssl' => true,
        ]));
    }

    public function test_allows_http_url_when_ssl_disabled(): void
    {
        $config = LsimConfiguration::fromArray($this->validConfig([
            'base_url'   => 'http://localhost:9000',
            'verify_ssl' => false,
        ]));

        $this->assertSame('http://localhost:9000', $config->getBaseUrl());
        $this->assertFalse($config->getVerifySsl());
    }

    public function test_throws_exception_for_timeout_too_low(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('timeout');

        LsimConfiguration::fromArray($this->validConfig(['timeout' => 0]));
    }

    public function test_throws_exception_for_timeout_too_high(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('timeout');

        LsimConfiguration::fromArray($this->validConfig(['timeout' => 301]));
    }

    public function test_validate_returns_true_for_valid_config(): void
    {
        $config = LsimConfiguration::fromArray($this->validConfig());

        $this->assertTrue($config->validate());
    }
}
