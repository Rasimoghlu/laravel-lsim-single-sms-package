<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\DataTransferObjects;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsResponse;
use Sarkhanrasimoghlu\Lsim\Enums\ErrorCode;

class SmsResponseTest extends TestCase
{
    public function test_success_response(): void
    {
        $response = SmsResponse::success('12345', ['obj' => '12345']);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->hasError());
        $this->assertSame('12345', $response->getMessageId());
        $this->assertNull($response->getErrorMessage());
        $this->assertNull($response->getErrorCode());
        $this->assertSame(['obj' => '12345'], $response->getRawResponse());
    }

    public function test_failure_response_with_error_code(): void
    {
        $response = SmsResponse::failure(
            'Invalid API key',
            ErrorCode::InvalidKey,
            ['errorCode' => -100]
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->hasError());
        $this->assertNull($response->getMessageId());
        $this->assertSame('Invalid API key', $response->getErrorMessage());
        $this->assertSame(ErrorCode::InvalidKey, $response->getErrorCode());
    }

    public function test_failure_response_without_error_code(): void
    {
        $response = SmsResponse::failure('Unknown error');

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getErrorCode());
        $this->assertSame('Unknown error', $response->getErrorMessage());
    }

    public function test_raw_response_is_preserved(): void
    {
        $raw = ['errorCode' => 0, 'obj' => '999', 'successMessage' => 'OK'];
        $response = SmsResponse::success('999', $raw);

        $this->assertSame($raw, $response->getRawResponse());
    }
}
