<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\DataTransferObjects;

/**
 * Balance Response Data Transfer Object
 * 
 * Immutable object representing a balance check response
 */
final readonly class BalanceResponse
{
    /**
     * Create a new balance response
     * 
     * @param bool $success Whether the balance check was successful
     * @param float|null $balance The account balance
     * @param string|null $currency The currency code
     * @param string|null $errorMessage Error message if failed
     * @param array<string, mixed> $rawResponse The raw API response
     */
    public function __construct(
        private bool $success,
        private ?float $balance = null,
        private ?string $currency = null,
        private ?string $errorMessage = null,
        private array $rawResponse = []
    ) {}

    /**
     * Check if the balance check was successful
     * 
     * @return bool True if successful
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Get the account balance
     * 
     * @return float|null The balance amount
     */
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    /**
     * Get the currency code
     * 
     * @return string|null The currency code
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
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
     * Get the raw API response
     * 
     * @return array<string, mixed> The raw response
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    /**
     * Check if balance is sufficient
     * 
     * @param float $requiredAmount The required amount
     * @return bool True if balance is sufficient
     */
    public function isSufficient(float $requiredAmount): bool
    {
        return $this->success && $this->balance !== null && $this->balance >= $requiredAmount;
    }

    /**
     * Check if balance is low (below threshold)
     * 
     * @param float $threshold The low balance threshold
     * @return bool True if balance is low
     */
    public function isLow(float $threshold = 10.0): bool
    {
        return $this->success && $this->balance !== null && $this->balance < $threshold;
    }

    /**
     * Create a successful response
     * 
     * @param float $balance The balance amount
     * @param string $currency The currency code
     * @param array<string, mixed> $rawResponse The raw response
     * @return self
     */
    public static function success(float $balance, string $currency = 'AZN', array $rawResponse = []): self
    {
        return new self(
            success: true,
            balance: $balance,
            currency: $currency,
            rawResponse: $rawResponse
        );
    }

    /**
     * Create a failed response
     * 
     * @param string $errorMessage The error message
     * @param array<string, mixed> $rawResponse The raw response
     * @return self
     */
    public static function failure(string $errorMessage, array $rawResponse = []): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            rawResponse: $rawResponse
        );
    }
}