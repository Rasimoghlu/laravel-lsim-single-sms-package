<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Tests\Unit\DataTransferObjects;

use PHPUnit\Framework\TestCase;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsMessage;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidMessageException;

/**
 * SMS Message Test
 */
class SmsMessageTest extends TestCase
{
    public function test_creates_valid_sms_message(): void
    {
        $message = new SmsMessage('Hello World', '+994501234567');
        
        $this->assertEquals('Hello World', $message->getText());
        $this->assertEquals('+994501234567', $message->getPhoneNumber());
        $this->assertEquals('994501234567', $message->getNormalizedPhoneNumber());
    }

    public function test_normalizes_phone_number(): void
    {
        $testCases = [
            ['+994501234567', '994501234567'],
            ['994501234567', '994501234567'],
            ['+1-234-567-8900', '12345678900'],
            ['+1 (234) 567-8900', '12345678900'],
        ];

        foreach ($testCases as [$input, $expected]) {
            $message = new SmsMessage('Test', $input);
            $this->assertEquals($expected, $message->getNormalizedPhoneNumber());
        }
    }

    public function test_throws_exception_for_empty_text(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Message text cannot be empty');
        
        new SmsMessage('', '+994501234567');
    }

    public function test_throws_exception_for_whitespace_only_text(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Message text cannot be empty');
        
        new SmsMessage('   ', '+994501234567');
    }

    public function test_throws_exception_for_too_long_text(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Message text cannot exceed 1000 characters');
        
        $longText = str_repeat('a', 1001);
        new SmsMessage($longText, '+994501234567');
    }

    public function test_throws_exception_for_empty_phone(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Phone number cannot be empty');
        
        new SmsMessage('Hello', '');
    }

    public function test_throws_exception_for_invalid_phone_format(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Invalid phone number format');
        
        new SmsMessage('Hello', 'invalid-phone');
    }

    public function test_throws_exception_for_too_short_phone(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Invalid phone number format. Must be 7-15 digits.');
        
        new SmsMessage('Hello', '123456'); // Only 6 digits
    }

    public function test_throws_exception_for_too_long_phone(): void
    {
        $this->expectException(InvalidMessageException::class);
        $this->expectExceptionMessage('Invalid phone number format. Must be 7-15 digits.');
        
        new SmsMessage('Hello', '1234567890123456'); // 16 digits
    }

    public function test_accepts_valid_phone_lengths(): void
    {
        // Test minimum valid length (7 digits)
        $message1 = new SmsMessage('Hello', '1234567');
        $this->assertEquals('1234567', $message1->getNormalizedPhoneNumber());

        // Test maximum valid length (15 digits)
        $message2 = new SmsMessage('Hello', '123456789012345');
        $this->assertEquals('123456789012345', $message2->getNormalizedPhoneNumber());
    }
}