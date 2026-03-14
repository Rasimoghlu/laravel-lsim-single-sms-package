<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\DataTransferObjects;

use Sarkhanrasimoghlu\Lsim\Enums\DeliveryStatus;

final readonly class ReportResponse
{
    public function __construct(
        public bool $success,
        public ?DeliveryStatus $status = null,
        public ?string $errorMessage = null,
        public array $rawResponse = [],
    ) {}

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function isDelivered(): bool
    {
        return $this->success && $this->status?->isDelivered() === true;
    }

    public function getStatus(): ?DeliveryStatus
    {
        return $this->status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    public static function success(DeliveryStatus $status, array $rawResponse = []): self
    {
        return new self(
            success: true,
            status: $status,
            rawResponse: $rawResponse,
        );
    }

    public static function failure(string $errorMessage, array $rawResponse = []): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            rawResponse: $rawResponse,
        );
    }
}
