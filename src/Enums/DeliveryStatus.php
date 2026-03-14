<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Enums;

enum DeliveryStatus: int
{
    case InQueue = 100;
    case Delivered = 101;
    case Undelivered = 102;
    case Expired = 103;
    case Rejected = 104;
    case Cancelled = 105;
    case Error = 106;
    case Unknown = 107;
    case Sent = 108;
    case BlackList = 109;

    public function description(): string
    {
        return match ($this) {
            self::InQueue => 'Message is in queue',
            self::Delivered => 'Message delivered successfully',
            self::Undelivered => 'Message could not be delivered',
            self::Expired => 'Message expired',
            self::Rejected => 'Message rejected',
            self::Cancelled => 'Message cancelled',
            self::Error => 'Delivery error',
            self::Unknown => 'Unknown delivery status',
            self::Sent => 'Message sent to operator',
            self::BlackList => 'Number is in blacklist',
        };
    }

    public function isDelivered(): bool
    {
        return $this === self::Delivered;
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Delivered,
            self::Undelivered,
            self::Expired,
            self::Rejected,
            self::Cancelled,
            self::Error,
            self::BlackList,
        ]);
    }
}
