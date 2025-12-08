<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Contracts;

use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsMessage;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsResponse;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\BalanceResponse;

/**
 * SMS Service Interface
 * 
 * Defines the contract for SMS service implementations
 */
interface SmsServiceInterface
{
    /**
     * Send an SMS message
     * 
     * @param SmsMessage $message The message to send
     * @return SmsResponse The response from the SMS service
     * @throws \Sarkhanrasimoghlu\Lsim\Exceptions\SmsException
     */
    public function send(SmsMessage $message): SmsResponse;

    /**
     * Check account balance
     * 
     * @return BalanceResponse The balance information
     * @throws \Sarkhanrasimoghlu\Lsim\Exceptions\BalanceException
     */
    public function getBalance(): BalanceResponse;
}