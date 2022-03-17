<?php


namespace Sarkhanrasimoghlu\Lsim\Controller;

use App\Http\Controllers\Controller;
use JsonException;
use Sarkhanrasimoghlu\Lsim\Facade\SmsFacade;
use Sarkhanrasimoghlu\Lsim\Traits\CheckBalanceTrait;

class SmsController extends Controller
{
    use CheckBalanceTrait;

    /**
     * @throws JsonException
     */
    public function showBalance()
    {
        $balanceResponse = json_decode(SmsFacade::checkBalance()->content(), true, 512, JSON_THROW_ON_ERROR);

        return view('sms::balance', compact('balanceResponse'));
    }
}
