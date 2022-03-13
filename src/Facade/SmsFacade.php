<?php

namespace Sarkhanrasimoghlu\Lsim\Facade;

use Illuminate\Support\Facades\Facade;

class SmsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sms';
    }
}
