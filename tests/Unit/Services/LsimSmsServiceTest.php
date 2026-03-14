<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Sarkhanrasimoghlu\Lsim\Configuration\LsimConfiguration;
use Sarkhanrasimoghlu\Lsim\Contracts\HttpClientInterface;
use Sarkhanrasimoghlu\Lsim\Enums\DeliveryStatus;
use Sarkhanrasimoghlu\Lsim\Enums\ErrorCode;
use Sarkhanrasimoghlu\Lsim\Exceptions\HttpException;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidMessageException;
use Sarkhanrasimoghlu\Lsim\Exceptions\SmsException;
use Sarkhanrasimoghlu\Lsim\Services\LsimSmsService;

class LsimSmsServiceTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private LsimConfiguration $config;
    private LsimSmsService $service;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->config = LsimConfiguration::fromArray([
            'login'      => 'test_login',
            'password'   => 'test_pass',
            'sender'     => 'TestSender',
            'base_url'   => 'https://apps.lsim.az/quicksms',
            'timeout'    => 30,
            'verify_ssl' => true,
        ]);
        $this->service = new LsimSmsService($this->httpClient, $this->config, new NullLogger());
    }

    // --- send() tests ---

    public function test_send_success(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url) {
                $this->assertStringContainsString('/v1/send?', $url);
                $this->assertStringContainsString('login=test_login', $url);
                $this->assertStringContainsString('msisdn=994501234567', $url);
                $this->assertStringContainsString('sender=TestSender', $url);
                $this->assertStringContainsString('key=', $url);
                $this->assertStringContainsString('unicode=0', $url);
                return true;
            }))
            ->willReturn(['errorCode' => 0, 'obj' => '12345', 'successMessage' => 'OK']);

        $response = $this->service->send('994501234567', 'Hello World');

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('12345', $response->getMessageId());
    }

    public function test_send_with_unicode(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url) {
                $this->assertStringContainsString('unicode=1', $url);
                return true;
            }))
            ->willReturn(['errorCode' => 0, 'obj' => '12345']);

        $response = $this->service->send('994501234567', 'Salam', true);

        $this->assertTrue($response->isSuccessful());
    }

    public function test_send_failure_with_error_code(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'errorCode' => -100,
                'errorMessage' => 'Invalid API key',
            ]);

        $response = $this->service->send('994501234567', 'Hello');

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(ErrorCode::InvalidKey, $response->getErrorCode());
        $this->assertSame('Invalid API key', $response->getErrorMessage());
    }

    public function test_send_failure_with_unknown_error_code(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'errorCode' => -999,
                'errorMessage' => 'Some unknown error',
            ]);

        $response = $this->service->send('994501234567', 'Hello');

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getErrorCode());
    }

    public function test_send_throws_exception_on_http_error(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willThrowException(HttpException::connectionFailed('https://example.com', 'Connection refused'));

        $this->expectException(SmsException::class);

        $this->service->send('994501234567', 'Hello');
    }

    public function test_send_validates_phone_number(): void
    {
        $this->expectException(InvalidMessageException::class);

        $this->service->send('invalid', 'Hello');
    }

    public function test_send_validates_empty_text(): void
    {
        $this->expectException(InvalidMessageException::class);

        $this->service->send('994501234567', '');
    }

    public function test_send_generates_correct_key(): void
    {
        $expectedKey = md5(md5('test_pass') . 'test_login' . 'Hello' . '994501234567' . 'TestSender');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url) use ($expectedKey) {
                $this->assertStringContainsString('key=' . $expectedKey, $url);
                return true;
            }))
            ->willReturn(['errorCode' => 0, 'obj' => '1']);

        $this->service->send('994501234567', 'Hello');
    }

    // --- getBalance() tests ---

    public function test_get_balance_success(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url) {
                $this->assertStringContainsString('/v1/balance?', $url);
                $this->assertStringContainsString('login=test_login', $url);
                $this->assertStringContainsString('key=', $url);
                return true;
            }))
            ->willReturn(['errorCode' => 0, 'obj' => 150]);

        $response = $this->service->getBalance();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(150, $response->getBalance());
    }

    public function test_get_balance_failure(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['errorCode' => -100, 'errorMessage' => 'Invalid key']);

        $response = $this->service->getBalance();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Invalid key', $response->getErrorMessage());
    }

    public function test_get_balance_throws_on_http_error(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willThrowException(HttpException::timeout(30));

        $this->expectException(SmsException::class);

        $this->service->getBalance();
    }

    // --- getReport() tests ---

    public function test_get_report_delivered(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url) {
                $this->assertStringContainsString('/v1/report?', $url);
                $this->assertStringContainsString('trans_id=12345', $url);
                return true;
            }))
            ->willReturn(['errorCode' => 0, 'obj' => 101]);

        $response = $this->service->getReport(12345);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isDelivered());
        $this->assertSame(DeliveryStatus::Delivered, $response->getStatus());
    }

    public function test_get_report_in_queue(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['errorCode' => 0, 'obj' => 100]);

        $response = $this->service->getReport(12345);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isDelivered());
        $this->assertSame(DeliveryStatus::InQueue, $response->getStatus());
    }

    public function test_get_report_unknown_status(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['errorCode' => 0, 'obj' => 999]);

        $response = $this->service->getReport(12345);

        $this->assertFalse($response->isSuccessful());
        $this->assertStringContainsString('Unknown delivery status', $response->getErrorMessage());
    }

    public function test_get_report_failure(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['errorCode' => -106, 'errorMessage' => 'Invalid transaction ID']);

        $response = $this->service->getReport(99999);

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Invalid transaction ID', $response->getErrorMessage());
    }

    public function test_get_report_throws_on_http_error(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willThrowException(HttpException::connectionFailed('https://example.com', 'timeout'));

        $this->expectException(SmsException::class);

        $this->service->getReport(12345);
    }

    // --- Null errorCode handling ---

    public function test_send_null_error_code_is_success(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['obj' => '555']);

        $response = $this->service->send('994501234567', 'Test');

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('555', $response->getMessageId());
    }

    public function test_balance_null_error_code_is_success(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['obj' => 200]);

        $response = $this->service->getBalance();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(200, $response->getBalance());
    }
}
