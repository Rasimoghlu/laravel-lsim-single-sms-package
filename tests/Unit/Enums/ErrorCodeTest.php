<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\Enums\ErrorCode;

class ErrorCodeTest extends TestCase
{
    public function test_all_error_codes_have_correct_values(): void
    {
        $this->assertSame(-100, ErrorCode::InvalidKey->value);
        $this->assertSame(-101, ErrorCode::TextTooLong->value);
        $this->assertSame(-102, ErrorCode::WrongNumberFormat->value);
        $this->assertSame(-103, ErrorCode::InvalidSenderName->value);
        $this->assertSame(-104, ErrorCode::InsufficientBalance->value);
        $this->assertSame(-105, ErrorCode::NumberInBlackList->value);
        $this->assertSame(-106, ErrorCode::InvalidTransactionId->value);
        $this->assertSame(-107, ErrorCode::IpNotAllowed->value);
        $this->assertSame(-108, ErrorCode::InvalidHash->value);
        $this->assertSame(-109, ErrorCode::NoHost->value);
        $this->assertSame(-110, ErrorCode::ReportingLimitExceeded->value);
        $this->assertSame(-500, ErrorCode::InternalError->value);
    }

    public function test_try_from_returns_correct_enum(): void
    {
        $this->assertSame(ErrorCode::InvalidKey, ErrorCode::tryFrom(-100));
        $this->assertSame(ErrorCode::InternalError, ErrorCode::tryFrom(-500));
        $this->assertNull(ErrorCode::tryFrom(-999));
    }

    public function test_description_returns_non_empty_string(): void
    {
        foreach (ErrorCode::cases() as $case) {
            $this->assertNotEmpty($case->description());
        }
    }
}
