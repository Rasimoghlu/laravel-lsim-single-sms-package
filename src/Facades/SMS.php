<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Facades;

use Illuminate\Support\Facades\Facade;
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;

/**
 * @method static \Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsResponse send(string $phone, string $text, bool $unicode = false)
 * @method static \Sarkhanrasimoghlu\Lsim\DataTransferObjects\BalanceResponse getBalance()
 * @method static \Sarkhanrasimoghlu\Lsim\DataTransferObjects\ReportResponse getReport(int $transactionId)
 *
 * @see \Sarkhanrasimoghlu\Lsim\Services\LsimSmsService
 */
class SMS extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmsServiceInterface::class;
    }
}
