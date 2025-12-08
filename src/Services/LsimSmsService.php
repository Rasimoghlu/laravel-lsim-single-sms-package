<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Services;

use Psr\Log\LoggerInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\ConfigurationInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\HttpClientInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\BalanceResponse;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsMessage;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsResponse;
use Sarkhanrasimoghlu\Lsim\Exceptions\BalanceException;
use Sarkhanrasimoghlu\Lsim\Exceptions\HttpException;
use Sarkhanrasimoghlu\Lsim\Exceptions\SmsException;

/**
 * L-sim SMS Service
 * 
 * Implementation of SMS service for L-sim API
 */
final class LsimSmsService implements SmsServiceInterface
{
    /**
     * Create a new L-sim SMS service
     * 
     * @param HttpClientInterface $httpClient HTTP client for API calls
     * @param ConfigurationInterface $config Service configuration
     * @param LoggerInterface $logger Logger for debugging and monitoring
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ConfigurationInterface $config,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * {@inheritDoc}
     */
    public function send(SmsMessage $message): SmsResponse
    {
        $this->logger->info('Attempting to send SMS', [
            'phone' => $message->getPhoneNumber(),
            'text_length' => mb_strlen($message->getText()),
        ]);

        try {
            $requestData = $this->prepareSmsRequest($message);
            
            $response = $this->httpClient->post(
                $this->config->getBaseUrl(),
                $requestData
            );

            return $this->parseSmsResponse($response);
        } catch (HttpException $e) {
            $this->logger->error('SMS sending failed due to HTTP error', [
                'error' => $e->getMessage(),
                'phone' => $message->getPhoneNumber(),
            ]);
            
            throw new SmsException('Failed to send SMS: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            $this->logger->error('SMS sending failed', [
                'error' => $e->getMessage(),
                'phone' => $message->getPhoneNumber(),
            ]);
            
            throw new SmsException('Unexpected error while sending SMS', 0, $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBalance(): BalanceResponse
    {
        if (!$this->config->isBalanceCheckEnabled()) {
            throw BalanceException::balanceCheckDisabled();
        }

        $this->logger->info('Checking account balance');

        try {
            $requestData = $this->prepareBalanceRequest();
            
            $response = $this->httpClient->get(
                $this->config->getBalanceUrl() . '?' . http_build_query($requestData)
            );

            return $this->parseBalanceResponse($response);
        } catch (HttpException $e) {
            $this->logger->error('Balance check failed due to HTTP error', [
                'error' => $e->getMessage(),
            ]);
            
            throw new BalanceException('Failed to check balance: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            $this->logger->error('Balance check failed', [
                'error' => $e->getMessage(),
            ]);
            
            throw new BalanceException('Unexpected error while checking balance', 0, $e);
        }
    }

    /**
     * Prepare SMS request data
     * 
     * @param SmsMessage $message The message to send
     * @return array<string, string> Request data
     */
    private function prepareSmsRequest(SmsMessage $message): array
    {
        return [
            'login' => $this->config->getLogin(),
            'msisdn' => $message->getNormalizedPhoneNumber(),
            'sender' => $this->config->getSender(),
            'text' => $message->getText(),
            'sign' => $this->generateSignature(
                $this->config->getPassword(),
                $this->config->getLogin(),
                $message->getText(),
                $message->getNormalizedPhoneNumber(),
                $this->config->getSender()
            ),
        ];
    }

    /**
     * Prepare balance request data
     * 
     * @return array<string, string> Request data
     */
    private function prepareBalanceRequest(): array
    {
        return [
            'login' => $this->config->getLogin(),
            'sign' => $this->generateBalanceSignature(
                $this->config->getPassword(),
                $this->config->getLogin()
            ),
        ];
    }

    /**
     * Generate signature for SMS API (MD5 as required by L-sim API)
     * 
     * @param string $password API password
     * @param string $login API login
     * @param string $text Message text
     * @param string $msisdn Phone number
     * @param string $sender Sender name
     * @return string Generated signature
     */
    private function generateSignature(string $password, string $login, string $text, string $msisdn, string $sender): string
    {
        // Using MD5 as required by L-sim API specification
        return md5(md5($password) . $login . $text . $msisdn . $sender);
    }

    /**
     * Generate signature for balance API (MD5 as required by L-sim API)
     * 
     * @param string $password API password
     * @param string $login API login
     * @return string Generated signature
     */
    private function generateBalanceSignature(string $password, string $login): string
    {
        // Using MD5 as required by L-sim API specification
        return md5(md5($password) . $login);
    }

    /**
     * Parse SMS API response
     * 
     * @param array<string, mixed> $response Raw API response
     * @return SmsResponse Parsed response
     */
    private function parseSmsResponse(array $response): SmsResponse
    {
        // L-sim API typically returns success/error status
        $isSuccess = isset($response['result']) && $response['result'] === 'success';
        
        if ($isSuccess) {
            $this->logger->info('SMS sent successfully', ['response' => $response]);
            
            return SmsResponse::success(
                messageId: (string) ($response['message_id'] ?? 'unknown'),
                rawResponse: $response
            );
        }

        $errorMessage = $response['error'] ?? 'Unknown error';
        $errorCode = isset($response['error_code']) ? (int) $response['error_code'] : null;
        
        $this->logger->warning('SMS sending failed', [
            'error' => $errorMessage,
            'error_code' => $errorCode,
            'response' => $response,
        ]);

        return SmsResponse::failure(
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            rawResponse: $response
        );
    }

    /**
     * Parse balance API response
     * 
     * @param array<string, mixed> $response Raw API response
     * @return BalanceResponse Parsed response
     */
    private function parseBalanceResponse(array $response): BalanceResponse
    {
        // Handle different response formats from L-sim API
        if (isset($response['balance'])) {
            $balance = (float) $response['balance'];
            
            $this->logger->info('Balance retrieved successfully', [
                'balance' => $balance,
                'response' => $response,
            ]);
            
            return BalanceResponse::success(
                balance: $balance,
                currency: $response['currency'] ?? 'AZN',
                rawResponse: $response
            );
        }

        if (isset($response['response']['obj'])) {
            $balance = (float) $response['response']['obj'];
            
            $this->logger->info('Balance retrieved successfully', [
                'balance' => $balance,
                'response' => $response,
            ]);
            
            return BalanceResponse::success(
                balance: $balance,
                currency: 'AZN',
                rawResponse: $response
            );
        }

        $errorMessage = $response['error'] ?? 'Could not retrieve balance';
        
        $this->logger->warning('Balance check failed', [
            'error' => $errorMessage,
            'response' => $response,
        ]);

        return BalanceResponse::failure(
            errorMessage: $errorMessage,
            rawResponse: $response
        );
    }
}