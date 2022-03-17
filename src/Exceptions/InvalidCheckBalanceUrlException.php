<?php


namespace Sarkhanrasimoghlu\Lsim\Exceptions;


use Exception;

class InvalidCheckBalanceUrlException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Please make sure SMS_CHECK_BALANCE_URL is true in your env file';

    /**
     * @var int
     */
    protected $code = 403;
}
