<?php

namespace Sarkhanrasimoghlu\Lsim\Facade;

use Illuminate\Support\Facades\Facade;
use Sarkhanrasimoghlu\Lsim\Sms;

class SmsFacade extends Facade
{
    /**
     * @method static send(string $text, string $phone)
     * @see Sms
     */
    protected static function getFacadeAccessor()
    {
        return 'Sms';
    }
}
