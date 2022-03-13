<?php

namespace Sarkhanrasimoghlu\Lsim;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Sms
 * @package Lsim\Sms
 */
class Sms
{
    protected $login;
    protected $password;
    protected $key;
    protected $msisdn;
    protected $text;
    protected $sender;
    protected $url;

    public function __construct()
    {
        $this->login = config('sms.credentials.login');
        $this->password = config('sms.credentials.password');
        $this->sender = config('sms.credentials.sender');
        $this->url = config('sms.credentials.url');
    }

    /**
     * @throws GuzzleException
     */
    public function send($text, $phone): void
    {
        $this->text = $text;
        $this->msisdn = $phone;
        $this->generateKey();
        $this->sendSmsRequest();
    }

    /**
     * @throws GuzzleException
     */
    public function sendSmsRequest(): void
    {
        $client = new Client();

        $client->request('GET', $this->url, ['query' => [
            'login' => $this->login,
            'msisdn' => $this->msisdn,
            'text' => $this->text,
            'sender' => $this->sender,
            'key' => $this->key,
            'unicode' => true
        ]]);
    }

    public function generateKey(): void
    {
        $this->key = md5(md5($this->password) . $this->login . $this->text . $this->msisdn . $this->sender);
    }

}
