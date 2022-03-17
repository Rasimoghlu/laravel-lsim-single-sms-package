<?php

namespace Sarkhanrasimoghlu\Lsim;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use JsonException;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidCheckBalanceUrlException;
use Sarkhanrasimoghlu\Lsim\Exceptions\NotBalanceException;
use Sarkhanrasimoghlu\Lsim\Traits\CheckBalanceTrait;
use Sarkhanrasimoghlu\Lsim\Traits\SendSmsTrait;

/**
 * Class Sms
 * @package Lsim\Sms
 */
class Sms
{
    use SendSmsTrait, CheckBalanceTrait;

    private $login;
    private $password;
    private $msisdn;
    private $sender;
    private $url;
    private $balanceUrl = 'http://apps.lsim.az/quicksms/v1/balance?';
    protected $text;
    protected $client;

    public function __construct()
    {
        $this->login = config('sms.credentials.login');
        $this->password = config('sms.credentials.password');
        $this->sender = config('sms.credentials.sender');
        $this->url = config('sms.credentials.url');
        $this->client = new Client();
    }

    /**
     * @param $text
     * @param $phone
     * @return mixed
     * @throws NotBalanceException
     */
    public function send($text, $phone)
    {
        return $this->sendSms($text, $phone);
    }

    /**
     * @throws InvalidCheckBalanceUrlException
     * @throws JsonException
     */
    public function checkBalance(): JsonResponse
    {
        return $this->balance();
    }

//    public function smsRoutes()
//    {
//        require __DIR__.'/../routes/web.php';
//    }

}
