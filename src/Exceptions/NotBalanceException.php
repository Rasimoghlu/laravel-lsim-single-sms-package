<?php


namespace Sarkhanrasimoghlu\Lsim\Exceptions;


use Exception;

class NotBalanceException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'There is not enough balance.';

    /**
     * @var int
     */
    protected $code = 403;
}
