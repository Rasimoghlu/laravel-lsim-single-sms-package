# Lsim Single SMS Package

[![Latest Version](https://img.shields.io/packagist/v/sarkhanrasimoghlu/lsim.svg?style=flat-square)](https://packagist.org/packages/sarkhanrasimoghlu/lsim)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/Rasimoghlu/laravel-lsim-single-sms-package/Tests/main?label=tests)](https://github.com/Rasimoghlu/laravel-lsim-single-sms-package/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sarkhanrasimoghlu/lsim.svg?style=flat-square)](https://packagist.org/packages/sarkhanrasimoghlu/lsim)

**Professional Laravel SMS package for L-sim SMS service integration built with SOLID principles and modern PHP standards.**

This package provides a clean, type-safe, and testable way to integrate L-sim SMS services into your Laravel applications. It features both modern dependency-injection-based API and legacy compatibility for existing projects.

## ✨ Features

- 🏗️ **SOLID Architecture** - Built with dependency injection and interface-driven design
- 🔒 **Type Safety** - Full PHP 8.3+ type declarations and strict typing
- 🧪 **100% Testable** - Comprehensive unit and integration tests included
- 📚 **Rich Documentation** - Complete PHPDoc and usage examples
- ⚡ **Performance Optimized** - Efficient HTTP client with configurable timeouts
- 🔄 **Clean Architecture** - No legacy code, pure modern implementation
- 🛡️ **Secure** - HTTPS enforcement and input validation
- 🔧 **Developer Friendly** - Clear error messages and debugging support

## Requirements

| Dependency | Version |
|------------|---------|
| PHP        | ^8.3    |
| Laravel    | ^12.0   |
| GuzzleHttp | ^7.8    |

## Installation

Install the package via Composer:

```bash
composer require sarkhanrasimoghlu/lsim
```

The package will automatically register its service provider thanks to Laravel's package auto-discovery.

## Configuration

### 1. Publish Configuration File

```bash
php artisan vendor:publish --tag=sms-config
```

### 2. Environment Variables

Add these variables to your `.env` file:

```env
SMS_LOGIN="Your API Login"
SMS_PASSWORD="Your API Password"
SMS_SENDER="Your API Sender Name"
SMS_BASE_URL="https://apps.lsim.az/quicksms/v1/send"
SMS_BALANCE_URL="https://apps.lsim.az/quicksms/v1/balance"
SMS_CHECK_BALANCE_URL=true
SMS_TIMEOUT=30
```

**⚠️ Security Note:** Always use HTTPS URLs for production environments.

## Usage

### Modern API (Recommended)

#### Basic SMS Sending

```php
<?php

use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsMessage;

class NotificationService
{
    public function __construct(
        private SmsServiceInterface $smsService
    ) {}

    public function sendWelcomeSms(string $phoneNumber, string $userName): void
    {
        $message = new SmsMessage(
            text: "Welcome to our platform, {$userName}!",
            phoneNumber: $phoneNumber
        );

        $response = $this->smsService->send($message);

        if ($response->isSuccessful()) {
            logger()->info('SMS sent successfully', [
                'message_id' => $response->getMessageId(),
                'phone' => $phoneNumber,
            ]);
        } else {
            logger()->error('SMS sending failed', [
                'error' => $response->getErrorMessage(),
                'error_code' => $response->getErrorCode(),
            ]);
        }
    }
}
```

#### Advanced SMS Sending with Error Handling

```php
<?php

use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsMessage;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidMessageException;
use Sarkhanrasimoghlu\Lsim\Exceptions\SmsException;

class SmsNotificationService
{
    public function __construct(
        private SmsServiceInterface $smsService
    ) {}

    public function sendOtpCode(string $phoneNumber, string $otpCode): bool
    {
        try {
            // Create message with validation
            $message = new SmsMessage(
                text: "Your OTP code is: {$otpCode}. Valid for 5 minutes.",
                phoneNumber: $phoneNumber
            );

            // Send SMS
            $response = $this->smsService->send($message);

            if ($response->isSuccessful()) {
                // Log success
                logger()->info('OTP SMS sent', [
                    'phone' => $phoneNumber,
                    'message_id' => $response->getMessageId(),
                ]);
                return true;
            }

            // Handle API errors
            logger()->warning('OTP SMS failed', [
                'phone' => $phoneNumber,
                'error' => $response->getErrorMessage(),
                'error_code' => $response->getErrorCode(),
            ]);

            return false;

        } catch (InvalidMessageException $e) {
            // Handle validation errors
            logger()->error('Invalid SMS message', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;

        } catch (SmsException $e) {
            // Handle service errors
            logger()->error('SMS service error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
```

#### Balance Management

```php
<?php

use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\Exceptions\BalanceException;

class BalanceMonitorService
{
    private const LOW_BALANCE_THRESHOLD = 10.0;

    public function __construct(
        private SmsServiceInterface $smsService
    ) {}

    public function checkBalanceStatus(): array
    {
        try {
            $response = $this->smsService->getBalance();

            if (!$response->isSuccessful()) {
                throw new BalanceException('Failed to retrieve balance: ' . $response->getErrorMessage());
            }

            $balance = $response->getBalance();
            $isLow = $response->isLow(self::LOW_BALANCE_THRESHOLD);

            if ($isLow) {
                // Send alert to administrators
                $this->alertLowBalance($balance);
            }

            return [
                'balance' => $balance,
                'currency' => $response->getCurrency(),
                'is_low' => $isLow,
                'can_send_sms' => $response->isSufficient(1.0),
            ];

        } catch (BalanceException $e) {
            logger()->error('Balance check failed', ['error' => $e->getMessage()]);
            
            return [
                'balance' => null,
                'currency' => null,
                'is_low' => false,
                'can_send_sms' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function alertLowBalance(float $balance): void
    {
        // Implementation for low balance alerts
        logger()->alert('SMS balance is low', ['balance' => $balance]);
    }
}
```


## Balance Check Web Interface

### Enable Balance Route

First, make sure balance checking is enabled in your configuration:

```env
SMS_CHECK_BALANCE_URL=true
```

### Access Balance Page

Visit `http://your-domain.com/sms-balance` to see your current balance in a web interface.

### Customize Balance View

Publish the view files to customize the balance page:

```bash
php artisan vendor:publish --tag=sms-views
```

The view will be published to `resources/views/vendor/sms/balance.blade.php`.

## Advanced Configuration


### Rate Limiting

Configure rate limiting to prevent abuse:

```php
// config/sms.php
'rate_limiting' => [
    'enabled' => env('SMS_RATE_LIMIT_ENABLED', false),
    'max_attempts' => env('SMS_RATE_LIMIT_MAX', 10),
    'decay_minutes' => env('SMS_RATE_LIMIT_DECAY', 1),
],
```

### Security Settings

```php
// config/sms.php
'security' => [
    'verify_ssl' => env('SMS_VERIFY_SSL', true),
    'allowed_recipients' => env('SMS_ALLOWED_RECIPIENTS'), // For testing
],
```

## Testing

This package includes comprehensive unit and integration tests.

### Running Tests

**Run all tests:**
```bash
composer test
```

**Run only unit tests:**
```bash
composer test-unit
```

**Run only feature/integration tests:**
```bash
composer test-feature
```

**Run tests with coverage report:**
```bash
composer test-coverage
```

**Run specific test:**
```bash
composer test-filter SmsMessageTest
```

### Test Structure

The package follows Laravel testing best practices:

- **Unit Tests** (`tests/Unit/`): Test individual classes and methods
- **Feature Tests** (`tests/Feature/`): Test full integration with Laravel framework
- **Test Base Class** (`tests/TestCase.php`): Provides common setup for package testing

### Writing Custom Tests

If you extend this package, you can use our base test case:

```php
<?php

namespace YourNamespace\Tests;

use Sarkhanrasimoghlu\Lsim\Tests\TestCase;

class YourCustomTest extends TestCase
{
    public function test_your_functionality(): void
    {
        // Your test code here
        $this->assertTrue(true);
    }
}
```


## Architecture

This package follows SOLID principles and modern PHP practices:

### Design Patterns Used

- **Dependency Injection**: All dependencies are injected via constructor
- **Interface Segregation**: Small, focused interfaces
- **Strategy Pattern**: Configurable HTTP client and SMS service implementations
- **Data Transfer Objects**: Immutable value objects for data transport
- **Controller Pattern**: Laravel controllers for HTTP endpoints

### Key Components

```
src/
├── Contracts/              # Interfaces (Dependency Inversion)
│   ├── SmsServiceInterface.php
│   ├── HttpClientInterface.php
│   └── ConfigurationInterface.php
├── Services/               # Business Logic (Single Responsibility)
│   └── LsimSmsService.php
├── DataTransferObjects/    # Immutable Data Objects
│   ├── SmsMessage.php
│   ├── SmsResponse.php
│   └── BalanceResponse.php
├── Configuration/          # Configuration Management
│   └── LsimConfiguration.php
├── Http/                   # HTTP Client Abstraction
│   └── GuzzleHttpClient.php
├── Exceptions/            # Custom Exception Hierarchy
│   ├── SmsException.php
│   ├── BalanceException.php
│   └── ...
└── Controller/            # HTTP Controllers
    └── SmsController.php
```

## Development

### Code Quality

**Run static analysis:**
```bash
composer analyse
```

**Check code style:**
```bash
composer check-style
```

**Fix code style:**
```bash
composer fix-style
```

### Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for your feature
4. Ensure all tests pass: `composer test`
5. Submit a pull request

## Error Handling

The package provides detailed error handling:

### Exception Hierarchy

```
SmsException (base)
├── InvalidMessageException
├── BalanceException
├── HttpException
└── InvalidConfigurationException
```

### Error Response Structure

```php
// Success Response
SmsResponse {
    +isSuccessful(): true
    +getMessageId(): "MSG123456"
    +getErrorMessage(): null
    +getRawResponse(): [...]
}

// Error Response
SmsResponse {
    +isSuccessful(): false
    +getMessageId(): null
    +getErrorMessage(): "Insufficient balance"
    +getErrorCode(): 101
    +getRawResponse(): [...]
}
```

## Logging

The package automatically logs important events:

- SMS sending attempts and results
- Balance check operations
- Configuration errors
- HTTP client errors

Configure logging in your `config/logging.php`:

```php
'channels' => [
    'sms' => [
        'driver' => 'single',
        'path' => storage_path('logs/sms.log'),
        'level' => 'info',
    ],
],
```

## Security

### Best Practices

1. **Always use HTTPS** for API endpoints
2. **Validate input** - The package validates phone numbers and message content
3. **Rate limiting** - Configure rate limits to prevent abuse
4. **Environment variables** - Never commit API credentials to version control
5. **Error handling** - Don't expose sensitive information in error messages

### Security Features

- Input validation for phone numbers and message content
- HTTPS enforcement for API communication
- Configurable SSL certificate verification
- Secure credential management via environment variables

## Performance

### Optimizations

- **HTTP Client Pooling**: Reuses connections when possible
- **Lazy Loading**: Services are instantiated only when needed
- **Efficient Validation**: Fast input validation with minimal overhead
- **Configurable Timeouts**: Prevent hanging requests

### Monitoring

Monitor your SMS usage:

```php
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;

// Check if you can send SMS before attempting
$balanceResponse = $smsService->getBalance();
if (!$balanceResponse->isSufficient(1.0)) {
    // Handle insufficient balance
    throw new InsufficientBalanceException();
}
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for recent changes.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

- **Documentation**: This README and inline PHPDoc comments
- **Issues**: [GitHub Issues](https://github.com/Rasimoghlu/laravel-lsim-single-sms-package/issues)
- **Discussions**: [GitHub Discussions](https://github.com/Rasimoghlu/laravel-lsim-single-sms-package/discussions)

## Credits

- **Author**: [Sarkhan Rasimoghlu](https://github.com/Rasimoghlu)
- **All Contributors**: [Contributors List](../../contributors)

---

**Built with ❤️ for the Laravel community**