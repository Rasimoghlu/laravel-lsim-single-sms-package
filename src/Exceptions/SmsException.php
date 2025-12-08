<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Exceptions;

use Exception;

/**
 * Base SMS Exception
 * 
 * Base class for all SMS-related exceptions
 */
class SmsException extends Exception
{
    /**
     * Create a new SMS exception
     * 
     * @param string $message The exception message
     * @param int $code The exception code
     * @param \Throwable|null $previous The previous exception
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the exception context for logging
     * 
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return [
            'exception' => static::class,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}