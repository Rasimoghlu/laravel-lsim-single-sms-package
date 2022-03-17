<?php


namespace Sarkhanrasimoghlu\Lsim\Traits;

use Illuminate\Http\JsonResponse;
use JsonException;
use Sarkhanrasimoghlu\Lsim\Exceptions\InvalidCheckBalanceUrlException;

trait CheckBalanceTrait
{
    /**
     * @throws InvalidCheckBalanceUrlException
     * @throws JsonException
     */
    public function balance(): JsonResponse
    {
        if (!config('sms.credentials.balanceUrl')) {
            throw new InvalidCheckBalanceUrlException();
        }

        $getResponse = $this->client->request('GET', $this->balanceUrl . http_build_query($this->checkBalanceQueryParams()));

        $response = $getResponse->getBody()->getContents();

        if ($this->isJson($response)) {
            $response = json_decode($response, 1, 512, JSON_THROW_ON_ERROR);
        }

        return $this->returnResponse($response, $getResponse);
    }

    /**
     * @throws JsonException
     */
    public function isJson($response)
    {
        json_decode($response, false, 512, JSON_THROW_ON_ERROR);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * @return bool
     * @throws InvalidCheckBalanceUrlException
     * @throws JsonException
     */
    public function hasBalance(): bool
    {
        return json_decode($this->balance()->getData()->response->obj, false, 512, JSON_THROW_ON_ERROR) > 0;
    }

    public function checkBalanceQueryParams(): array
    {
        return [
            'login' => $this->login,
            'key' => $this->generateKeyForCheckBalance()
        ];
    }

    private function generateKeyForCheckBalance(): string
    {
        return md5(md5($this->password) . $this->login);
    }

    /**
     * @param $response
     * @param $getResponse
     * @return JsonResponse
     */
    public function returnResponse($response, $getResponse): JsonResponse
    {
        return response()->json([
            'response' => $response,
            'status_code' => $getResponse->getStatusCode()
        ], 200);
    }

}
