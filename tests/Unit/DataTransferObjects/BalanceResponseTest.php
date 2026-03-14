<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\DataTransferObjects;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\BalanceResponse;

class BalanceResponseTest extends TestCase
{
    public function test_success_response(): void
    {
        $response = BalanceResponse::success(150, ['obj' => 150]);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(150, $response->getBalance());
        $this->assertNull($response->getErrorMessage());
    }

    public function test_failure_response(): void
    {
        $response = BalanceResponse::failure('API error', ['errorCode' => -100]);

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getBalance());
        $this->assertSame('API error', $response->getErrorMessage());
    }

    public function test_is_sufficient(): void
    {
        $response = BalanceResponse::success(100);

        $this->assertTrue($response->isSufficient(50));
        $this->assertTrue($response->isSufficient(100));
        $this->assertFalse($response->isSufficient(101));
    }

    public function test_is_low(): void
    {
        $lowBalance = BalanceResponse::success(5);
        $highBalance = BalanceResponse::success(100);

        $this->assertTrue($lowBalance->isLow());
        $this->assertFalse($highBalance->isLow());
        $this->assertTrue($highBalance->isLow(200));
    }

    public function test_failed_response_is_not_sufficient(): void
    {
        $response = BalanceResponse::failure('Error');

        $this->assertFalse($response->isSufficient(1));
    }

    public function test_raw_response_is_preserved(): void
    {
        $raw = ['errorCode' => 0, 'obj' => 50];
        $response = BalanceResponse::success(50, $raw);

        $this->assertSame($raw, $response->getRawResponse());
    }
}
