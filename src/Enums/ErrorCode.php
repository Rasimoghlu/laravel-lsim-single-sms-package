<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Enums;

enum ErrorCode: int
{
    case InvalidKey = -100;
    case TextTooLong = -101;
    case WrongNumberFormat = -102;
    case InvalidSenderName = -103;
    case InsufficientBalance = -104;
    case NumberInBlackList = -105;
    case InvalidTransactionId = -106;
    case IpNotAllowed = -107;
    case InvalidHash = -108;
    case NoHost = -109;
    case ReportingLimitExceeded = -110;
    case InternalError = -500;

    public function description(): string
    {
        return match ($this) {
            self::InvalidKey => 'Invalid API key',
            self::TextTooLong => 'Message text is too long',
            self::WrongNumberFormat => 'Wrong phone number format',
            self::InvalidSenderName => 'Invalid sender name',
            self::InsufficientBalance => 'Insufficient account balance',
            self::NumberInBlackList => 'Phone number is in the blacklist',
            self::InvalidTransactionId => 'Invalid transaction ID',
            self::IpNotAllowed => 'IP address is not allowed',
            self::InvalidHash => 'Invalid hash signature',
            self::NoHost => 'No host available',
            self::ReportingLimitExceeded => 'Reporting limit exceeded',
            self::InternalError => 'Internal server error',
        };
    }
}
