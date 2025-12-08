<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\DataTransferObjects;

use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidMessageException;

/**
 * SMS Message Data Transfer Object
 * 
 * Immutable object representing an SMS message
 */
final readonly class SmsMessage
{
    /**
     * Create a new SMS message
     * 
     * @param string $text The message content
     * @param string $phoneNumber The recipient's phone number
     * @throws InvalidMessageException If the message or phone number is invalid
     */
    public function __construct(
        private string $text,
        private string $phoneNumber
    ) {
        $this->validateText($text);
        $this->validatePhoneNumber($phoneNumber);
    }

    /**
     * Get the message text
     * 
     * @return string The message content
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get the phone number
     * 
     * @return string The phone number
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * Get normalized phone number (digits only)
     * 
     * @return string The normalized phone number
     */
    public function getNormalizedPhoneNumber(): string
    {
        return str_replace(['+', '-', ' ', '(', ')'], '', $this->phoneNumber);
    }

    /**
     * Validate message text
     * 
     * @param string $text The text to validate
     * @throws InvalidMessageException If text is invalid
     */
    private function validateText(string $text): void
    {
        if (empty(trim($text))) {
            throw new InvalidMessageException('Message text cannot be empty');
        }

        if (mb_strlen($text) > 1000) {
            throw new InvalidMessageException('Message text cannot exceed 1000 characters');
        }
    }

    /**
     * Validate phone number
     * 
     * @param string $phoneNumber The phone number to validate
     * @throws InvalidMessageException If phone number is invalid
     */
    private function validatePhoneNumber(string $phoneNumber): void
    {
        $normalized = str_replace(['+', '-', ' ', '(', ')'], '', $phoneNumber);
        
        if (empty($normalized)) {
            throw new InvalidMessageException('Phone number cannot be empty');
        }

        if (!preg_match('/^\d{7,15}$/', $normalized)) {
            throw new InvalidMessageException('Invalid phone number format. Must be 7-15 digits.');
        }
    }
}