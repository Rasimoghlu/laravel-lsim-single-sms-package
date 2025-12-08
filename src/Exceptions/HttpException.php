<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Exceptions;

/**
 * HTTP Exception
 * 
 * Thrown when HTTP operations fail
 */
final class HttpException extends SmsException
{
    /**
     * Create exception for connection timeout
     * 
     * @param int $timeout The timeout value
     * @return self
     */
    public static function timeout(int $timeout): self
    {
        return new self("HTTP request timed out after {$timeout} seconds");
    }

    /**
     * Create exception for connection failure
     * 
     * @param string $url The URL that failed
     * @param string $reason The failure reason
     * @return self
     */
    public static function connectionFailed(string $url, string $reason): self
    {
        return new self("Failed to connect to {$url}: {$reason}");
    }

    /**
     * Create exception for HTTP error status
     * 
     * @param int $statusCode The HTTP status code
     * @param string $url The URL
     * @return self
     */
    public static function httpError(int $statusCode, string $url): self
    {
        return new self("HTTP {$statusCode} error for {$url}");
    }

    /**
     * Create exception for invalid response
     * 
     * @param string $reason The reason why response is invalid
     * @return self
     */
    public static function invalidResponse(string $reason): self
    {
        return new self("Invalid HTTP response: {$reason}");
    }
}