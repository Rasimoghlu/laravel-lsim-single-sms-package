<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Contracts;

use Sarkhanrasimoghlu\Lsim\DataTransferObjects\BalanceResponse;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\ReportResponse;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsResponse;

interface SmsServiceInterface
{
    public function send(string $phone, string $text, bool $unicode = false): SmsResponse;

    public function getBalance(): BalanceResponse;

    public function getReport(int $transactionId): ReportResponse;
}
