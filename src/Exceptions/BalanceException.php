<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Exceptions;

/**
 * Balance Exception
 * 
 * Thrown when balance check operations fail
 */
final class BalanceException extends SmsException
{
    /**
     * Create exception for balance check disabled
     * 
     * @return self
     */
    public static function balanceCheckDisabled(): self
    {
        return new self('Balance checking is disabled. Please enable SMS_CHECK_BALANCE_URL in your configuration.');
    }

    /**
     * Create exception for insufficient balance
     * 
     * @param float $required Required balance
     * @param float $current Current balance
     * @return self
     */
    public static function insufficientBalance(float $required, float $current): self
    {
        return new self("Insufficient balance. Required: {$required}, Current: {$current}");
    }

    /**
     * Create exception for balance check API failure
     * 
     * @param string $reason The failure reason
     * @return self
     */
    public static function apiFailure(string $reason): self
    {
        return new self("Balance check API failed: {$reason}");
    }
}