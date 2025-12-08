<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Exceptions;

/**
 * Invalid Message Exception
 * 
 * Thrown when SMS message validation fails
 */
final class InvalidMessageException extends SmsException
{
    /**
     * Create exception for empty message
     * 
     * @return self
     */
    public static function emptyMessage(): self
    {
        return new self('SMS message cannot be empty');
    }

    /**
     * Create exception for message too long
     * 
     * @param int $maxLength Maximum allowed length
     * @param int $actualLength Actual message length
     * @return self
     */
    public static function tooLong(int $maxLength, int $actualLength): self
    {
        return new self("SMS message too long. Maximum {$maxLength} characters, got {$actualLength}");
    }

    /**
     * Create exception for invalid phone number
     * 
     * @param string $phoneNumber The invalid phone number
     * @return self
     */
    public static function invalidPhoneNumber(string $phoneNumber): self
    {
        return new self("Invalid phone number format: {$phoneNumber}");
    }
}