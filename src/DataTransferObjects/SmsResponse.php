<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\DataTransferObjects;

/**
 * SMS Response Data Transfer Object
 * 
 * Immutable object representing an SMS API response
 */
final readonly class SmsResponse
{
    /**
     * Create a new SMS response
     * 
     * @param bool $success Whether the SMS was sent successfully
     * @param string|null $messageId The message ID if successful
     * @param string|null $errorMessage Error message if failed
     * @param int|null $errorCode Error code if failed
     * @param array<string, mixed> $rawResponse The raw API response
     */
    public function __construct(
        private bool $success,
        private ?string $messageId = null,
        private ?string $errorMessage = null,
        private ?int $errorCode = null,
        private array $rawResponse = []
    ) {}

    /**
     * Check if the SMS was sent successfully
     * 
     * @return bool True if successful
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Get the message ID
     * 
     * @return string|null The message ID
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * Get the error message
     * 
     * @return string|null The error message
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Get the error code
     * 
     * @return int|null The error code
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Get the raw API response
     * 
     * @return array<string, mixed> The raw response
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    /**
     * Check if there's an error
     * 
     * @return bool True if there's an error
     */
    public function hasError(): bool
    {
        return !$this->success;
    }

    /**
     * Create a successful response
     * 
     * @param string $messageId The message ID
     * @param array<string, mixed> $rawResponse The raw response
     * @return self
     */
    public static function success(string $messageId, array $rawResponse = []): self
    {
        return new self(
            success: true,
            messageId: $messageId,
            rawResponse: $rawResponse
        );
    }

    /**
     * Create a failed response
     * 
     * @param string $errorMessage The error message
     * @param int|null $errorCode The error code
     * @param array<string, mixed> $rawResponse The raw response
     * @return self
     */
    public static function failure(string $errorMessage, ?int $errorCode = null, array $rawResponse = []): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            rawResponse: $rawResponse
        );
    }
}