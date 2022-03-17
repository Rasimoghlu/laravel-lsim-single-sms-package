<?php


namespace Sarkhanrasimoghlu\Lsim\Traits;

use Sarkhanrasimoghlu\Lsim\Exceptions\NotBalanceException;

trait SendSmsTrait
{
    /**
     * @param string $text
     * @param string $phone
     * @return mixed
     * @throws NotBalanceException
     */
    public function sendSms(string $text, string $phone): mixed
    {
        if (!$this->hasBalance()) {
            throw new NotBalanceException();
        }

        $this->text = $text;
        $this->msisdn = str_replace('+', '', $phone);

        return $this->sendSmsRequest();
    }

    public function sendSmsRequest()
    {
        return $this->client->request('GET', $this->url . http_build_query($this->sendSmsQueryParams()));
    }

    private function sendSmsQueryParams(): array
    {
        return [
            'login' => $this->login,
            'msisdn' => $this->msisdn,
            'text' => $this->text,
            'sender' => $this->sender,
            'key' => $this->generateKeyForSendSms(),
            'unicode' => true
        ];
    }

    private function generateKeyForSendSms(): string
    {
        return md5(md5($this->password) . $this->login . $this->text . $this->msisdn . $this->sender);
    }

}
