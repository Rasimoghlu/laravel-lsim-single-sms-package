<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\DataTransferObjects;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\ReportResponse;
use Sarkhanrasimoghlu\Lsim\Enums\DeliveryStatus;

class ReportResponseTest extends TestCase
{
    public function test_success_response_with_delivered_status(): void
    {
        $response = ReportResponse::success(DeliveryStatus::Delivered);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isDelivered());
        $this->assertSame(DeliveryStatus::Delivered, $response->getStatus());
        $this->assertNull($response->getErrorMessage());
    }

    public function test_success_response_with_pending_status(): void
    {
        $response = ReportResponse::success(DeliveryStatus::InQueue);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isDelivered());
        $this->assertSame(DeliveryStatus::InQueue, $response->getStatus());
    }

    public function test_failure_response(): void
    {
        $response = ReportResponse::failure('Invalid transaction ID');

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isDelivered());
        $this->assertNull($response->getStatus());
        $this->assertSame('Invalid transaction ID', $response->getErrorMessage());
    }

    public function test_raw_response_is_preserved(): void
    {
        $raw = ['errorCode' => 0, 'obj' => 101];
        $response = ReportResponse::success(DeliveryStatus::Delivered, $raw);

        $this->assertSame($raw, $response->getRawResponse());
    }
}
