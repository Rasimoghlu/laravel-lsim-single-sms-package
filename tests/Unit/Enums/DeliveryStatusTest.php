<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\Enums\DeliveryStatus;

class DeliveryStatusTest extends TestCase
{
    public function test_all_statuses_have_correct_values(): void
    {
        $this->assertSame(100, DeliveryStatus::InQueue->value);
        $this->assertSame(101, DeliveryStatus::Delivered->value);
        $this->assertSame(102, DeliveryStatus::Undelivered->value);
        $this->assertSame(103, DeliveryStatus::Expired->value);
        $this->assertSame(104, DeliveryStatus::Rejected->value);
        $this->assertSame(105, DeliveryStatus::Cancelled->value);
        $this->assertSame(106, DeliveryStatus::Error->value);
        $this->assertSame(107, DeliveryStatus::Unknown->value);
        $this->assertSame(108, DeliveryStatus::Sent->value);
        $this->assertSame(109, DeliveryStatus::BlackList->value);
    }

    public function test_try_from_returns_correct_enum(): void
    {
        $this->assertSame(DeliveryStatus::Delivered, DeliveryStatus::tryFrom(101));
        $this->assertSame(DeliveryStatus::InQueue, DeliveryStatus::tryFrom(100));
        $this->assertNull(DeliveryStatus::tryFrom(999));
    }

    public function test_is_delivered(): void
    {
        $this->assertTrue(DeliveryStatus::Delivered->isDelivered());
        $this->assertFalse(DeliveryStatus::InQueue->isDelivered());
        $this->assertFalse(DeliveryStatus::Undelivered->isDelivered());
    }

    public function test_is_final(): void
    {
        $this->assertTrue(DeliveryStatus::Delivered->isFinal());
        $this->assertTrue(DeliveryStatus::Undelivered->isFinal());
        $this->assertTrue(DeliveryStatus::Expired->isFinal());
        $this->assertTrue(DeliveryStatus::Rejected->isFinal());
        $this->assertTrue(DeliveryStatus::BlackList->isFinal());
        $this->assertFalse(DeliveryStatus::InQueue->isFinal());
        $this->assertFalse(DeliveryStatus::Sent->isFinal());
        $this->assertFalse(DeliveryStatus::Unknown->isFinal());
    }

    public function test_description_returns_non_empty_string(): void
    {
        foreach (DeliveryStatus::cases() as $case) {
            $this->assertNotEmpty($case->description());
        }
    }
}
