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
    private $login;
    private $password;
    private $key;
    private $msisdn;
    private $sender;
    private $url;
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
     * @throws GuzzleException
     * @param $text
     * @param $phone
     */
    public function send($text, $phone)
    {
        $this->text = $text;
        $this->msisdn = $phone;
        $this->generateKey();
        return $this->sendSmsRequest();
    }

    /**
     * @throws GuzzleException
     */
    public function sendSmsRequest()
    {
        return $this->client->request('GET', $this->url, ['query' => [
            'login' => $this->login,
            'msisdn' => $this->msisdn,
            'text' => $this->text,
            'sender' => $this->sender,
            'key' => $this->key,
            'unicode' => true
        ]]);
    }

    private function generateKey(): void
    {
        $this->key = md5(md5($this->password) . $this->login . $this->text . $this->msisdn . $this->sender);
    }

}
