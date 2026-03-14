# LSIM SMS Package for Laravel

[![Latest Version](https://img.shields.io/packagist/v/sarkhanrasimoghlu/lsim.svg?style=flat-square)](https://packagist.org/packages/sarkhanrasimoghlu/lsim)
[![Total Downloads](https://img.shields.io/packagist/dt/sarkhanrasimoghlu/lsim.svg?style=flat-square)](https://packagist.org/packages/sarkhanrasimoghlu/lsim)

Laravel package for LSIM SMS gateway integration. Send SMS, check balance, and track delivery reports.

## Requirements

| Dependency | Version |
|------------|---------|
| PHP        | ^8.3    |
| Laravel    | ^12.0   |
| GuzzleHttp | ^7.8    |

## Installation

```bash
composer require sarkhanrasimoghlu/lsim
```

The service provider and `SMS` facade are registered automatically via package auto-discovery.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=sms-config
```

Add the following to your `.env` file:

```env
LSIM_LOGIN=your_login
LSIM_PASSWORD=your_password
LSIM_SENDER=your_sender_name
```

### All Configuration Options

| Key | Env Variable | Default | Description |
|-----|-------------|---------|-------------|
| `login` | `LSIM_LOGIN` | - | API login (required) |
| `password` | `LSIM_PASSWORD` | - | API password (required) |
| `sender` | `LSIM_SENDER` | - | Sender name (required) |
| `base_url` | `LSIM_BASE_URL` | `https://apps.lsim.az/quicksms` | API base URL |
| `timeout` | `LSIM_TIMEOUT` | `30` | HTTP timeout in seconds |
| `verify_ssl` | `LSIM_VERIFY_SSL` | `true` | Verify SSL certificates |
| `logging.channel` | `LSIM_LOG_CHANNEL` | `stack` | Log channel |
| `logging.level` | `LSIM_LOG_LEVEL` | `info` | Log level |

## Usage

### Send SMS

```php
use Sarkhanrasimoghlu\Lsim\Facades\SMS;

$response = SMS::send('994501234567', 'Hello World');

if ($response->isSuccessful()) {
    echo $response->getMessageId(); // transaction ID
}
```

### Send Unicode SMS

```php
$response = SMS::send('994501234567', 'Salam dunya', unicode: true);
```

### Check Balance

```php
$response = SMS::getBalance();

if ($response->isSuccessful()) {
    echo $response->getBalance(); // integer
}
```

### Get Delivery Report

```php
$response = SMS::getReport($transactionId);

if ($response->isSuccessful()) {
    echo $response->getStatus()->name; // e.g. "Delivered"
    echo $response->isDelivered();     // true/false
}
```

### Using Dependency Injection

```php
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;

class NotificationService
{
    public function __construct(
        private SmsServiceInterface $sms,
    ) {}

    public function notify(string $phone, string $text): bool
    {
        $response = $this->sms->send($phone, $text);
        return $response->isSuccessful();
    }
}
```

### Error Handling

```php
use Sarkhanrasimoghlu\Lsim\Facades\SMS;
use Sarkhanrasimoghlu\Lsim\Exceptions\SmsException;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidMessageException;

try {
    $response = SMS::send($phone, $text);

    if ($response->hasError()) {
        $errorCode = $response->getErrorCode(); // ErrorCode enum or null
        $errorMessage = $response->getErrorMessage();
    }
} catch (InvalidMessageException $e) {
    // Invalid phone number or message text
} catch (SmsException $e) {
    // HTTP or other service error
}
```

## Error Codes

| Code | Enum Case | Description |
|------|-----------|-------------|
| -100 | `InvalidKey` | Invalid API key |
| -101 | `TextTooLong` | Message text is too long |
| -102 | `WrongNumberFormat` | Wrong phone number format |
| -103 | `InvalidSenderName` | Invalid sender name |
| -104 | `InsufficientBalance` | Insufficient account balance |
| -105 | `NumberInBlackList` | Phone number is in the blacklist |
| -106 | `InvalidTransactionId` | Invalid transaction ID |
| -107 | `IpNotAllowed` | IP address is not allowed |
| -108 | `InvalidHash` | Invalid hash signature |
| -109 | `NoHost` | No host available |
| -110 | `ReportingLimitExceeded` | Reporting limit exceeded |
| -500 | `InternalError` | Internal server error |

## Delivery Statuses

| Code | Enum Case | Description |
|------|-----------|-------------|
| 100 | `InQueue` | Message is in queue |
| 101 | `Delivered` | Message delivered successfully |
| 102 | `Undelivered` | Message could not be delivered |
| 103 | `Expired` | Message expired |
| 104 | `Rejected` | Message rejected |
| 105 | `Cancelled` | Message cancelled |
| 106 | `Error` | Delivery error |
| 107 | `Unknown` | Unknown delivery status |
| 108 | `Sent` | Message sent to operator |
| 109 | `BlackList` | Number is in blacklist |

## API Endpoints

All endpoints use GET requests. The package appends these paths to the `base_url`:

| Endpoint | Path | Description |
|----------|------|-------------|
| Send SMS | `/v1/send` | Send a single SMS |
| Balance | `/v1/balance` | Check account balance |
| Report | `/v1/report` | Get delivery report by transaction ID |

## Testing

```bash
composer test
```

Run with coverage:

```bash
composer test-coverage
```

## Exception Hierarchy

```
SmsException (base)
├── InvalidMessageException
├── BalanceException
├── HttpException
└── InvalidConfigurationException
```

## License

MIT. See [LICENSE.md](LICENSE.md) for details.
