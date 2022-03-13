# Lsim Single Sms Package

### You can easily integrate L-sim sms integration with this package

## Requirements

| Lang       | Version |
| ---------- | ---- |
| PHP        | 8.*  |
| Laravel    | 9.*  |

## Installation

```
composer require sarkhanrasimoghlu/lsim
```

## Configuration Env File

```
SMS_LOGIN="Your Api Login"
SMS_PASSWORD="Your Api Password"
SMS_SENDER="Your Api Sender Name"
SMS_BASE_URL="Your Api Url"
```

## Usage

```ruby
$sms = new \Sarkhanrasimoghlu\Lsim\Sms();

$sms->send('sms text', 'phone');

```



