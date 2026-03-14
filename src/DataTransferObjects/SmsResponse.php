<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\DataTransferObjects;

use Sarkhanrasimoghlu\Lsim\Enums\ErrorCode;

final readonly class SmsResponse
{
    public function __construct(
        public bool $success,
        public ?string $messageId = null,
        public ?string $errorMessage = null,
        public ?ErrorCode $errorCode = null,
        public array $rawResponse = [],
    ) {}

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getErrorCode(): ?ErrorCode
    {
        return $this->errorCode;
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    public function hasError(): bool
    {
        return !$this->success;
    }

    public static function success(string $messageId, array $rawResponse = []): self
    {
        return new self(
            success: true,
            messageId: $messageId,
            rawResponse: $rawResponse,
        );
    }

    public static function failure(string $errorMessage, ?ErrorCode $errorCode = null, array $rawResponse = []): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            rawResponse: $rawResponse,
        );
    }
}
