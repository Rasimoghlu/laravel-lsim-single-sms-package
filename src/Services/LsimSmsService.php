<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Services;

use Psr\Log\LoggerInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\ConfigurationInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\HttpClientInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\BalanceResponse;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\ReportResponse;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsMessage;
use Sarkhanrasimoghlu\Lsim\DataTransferObjects\SmsResponse;
use Sarkhanrasimoghlu\Lsim\Enums\DeliveryStatus;
use Sarkhanrasimoghlu\Lsim\Enums\ErrorCode;
use Sarkhanrasimoghlu\Lsim\Exceptions\HttpException;
use Sarkhanrasimoghlu\Lsim\Exceptions\SmsException;

final class LsimSmsService implements SmsServiceInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ConfigurationInterface $config,
        private readonly LoggerInterface $logger,
    ) {}

    public function send(string $phone, string $text, bool $unicode = false): SmsResponse
    {
        $message = new SmsMessage($text, $phone);
        $msisdn = $message->getNormalizedPhoneNumber();

        $this->logger->info('Attempting to send SMS', [
            'phone' => $phone,
            'text_length' => mb_strlen($text),
        ]);

        try {
            $params = [
                'login'   => $this->config->getLogin(),
                'msisdn'  => $msisdn,
                'text'    => $text,
                'sender'  => $this->config->getSender(),
                'key'     => $this->generateSmsSignature($msisdn, $text),
                'unicode' => $unicode ? '1' : '0',
            ];

            $url = $this->config->getBaseUrl() . '/v1/send?' . http_build_query($params);
            $response = $this->httpClient->get($url);

            return $this->parseSmsResponse($response);
        } catch (HttpException $e) {
            $this->logger->error('SMS sending failed due to HTTP error', [
                'error' => $e->getMessage(),
                'phone' => $phone,
            ]);

            throw new SmsException('Failed to send SMS: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getBalance(): BalanceResponse
    {
        $this->logger->info('Checking account balance');

        try {
            $params = [
                'login' => $this->config->getLogin(),
                'key'   => $this->generateBalanceSignature(),
            ];

            $url = $this->config->getBaseUrl() . '/v1/balance?' . http_build_query($params);
            $response = $this->httpClient->get($url);

            return $this->parseBalanceResponse($response);
        } catch (HttpException $e) {
            $this->logger->error('Balance check failed due to HTTP error', [
                'error' => $e->getMessage(),
            ]);

            throw new SmsException('Failed to check balance: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getReport(int $transactionId): ReportResponse
    {
        $this->logger->info('Checking delivery report', [
            'transaction_id' => $transactionId,
        ]);

        try {
            $params = [
                'login'    => $this->config->getLogin(),
                'trans_id' => $transactionId,
                'key'      => $this->generateBalanceSignature(),
            ];

            $url = $this->config->getBaseUrl() . '/v1/report?' . http_build_query($params);
            $response = $this->httpClient->get($url);

            return $this->parseReportResponse($response);
        } catch (HttpException $e) {
            $this->logger->error('Report check failed due to HTTP error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            throw new SmsException('Failed to get report: ' . $e->getMessage(), 0, $e);
        }
    }

    private function generateSmsSignature(string $msisdn, string $text): string
    {
        return md5(
            md5($this->config->getPassword())
            . $this->config->getLogin()
            . $text
            . $msisdn
            . $this->config->getSender()
        );
    }

    private function generateBalanceSignature(): string
    {
        return md5(md5($this->config->getPassword()) . $this->config->getLogin());
    }

    private function parseSmsResponse(array $response): SmsResponse
    {
        $errorCode = isset($response['errorCode']) ? (int) $response['errorCode'] : null;

        if ($errorCode === null || $errorCode === 0) {
            $messageId = (string) ($response['obj'] ?? '');

            $this->logger->info('SMS sent successfully', [
                'message_id' => $messageId,
            ]);

            return SmsResponse::success(
                messageId: $messageId,
                rawResponse: $response,
            );
        }

        $errorEnum = ErrorCode::tryFrom($errorCode);
        $errorMessage = $response['errorMessage'] ?? ($errorEnum?->description() ?? 'Unknown error');

        $this->logger->warning('SMS sending failed', [
            'error' => $errorMessage,
            'error_code' => $errorCode,
        ]);

        return SmsResponse::failure(
            errorMessage: $errorMessage,
            errorCode: $errorEnum,
            rawResponse: $response,
        );
    }

    private function parseBalanceResponse(array $response): BalanceResponse
    {
        $errorCode = isset($response['errorCode']) ? (int) $response['errorCode'] : null;

        if ($errorCode === null || $errorCode === 0) {
            $balance = (int) ($response['obj'] ?? 0);

            $this->logger->info('Balance retrieved successfully', [
                'balance' => $balance,
            ]);

            return BalanceResponse::success(
                balance: $balance,
                rawResponse: $response,
            );
        }

        $errorMessage = $response['errorMessage'] ?? 'Could not retrieve balance';

        $this->logger->warning('Balance check failed', [
            'error' => $errorMessage,
            'error_code' => $errorCode,
        ]);

        return BalanceResponse::failure(
            errorMessage: $errorMessage,
            rawResponse: $response,
        );
    }

    private function parseReportResponse(array $response): ReportResponse
    {
        $errorCode = isset($response['errorCode']) ? (int) $response['errorCode'] : null;

        if ($errorCode === null || $errorCode === 0) {
            $statusCode = isset($response['obj']) ? (int) $response['obj'] : null;
            $status = $statusCode !== null ? DeliveryStatus::tryFrom($statusCode) : null;

            if ($status === null) {
                return ReportResponse::failure(
                    errorMessage: 'Unknown delivery status: ' . ($statusCode ?? 'null'),
                    rawResponse: $response,
                );
            }

            $this->logger->info('Report retrieved successfully', [
                'status' => $status->name,
            ]);

            return ReportResponse::success(
                status: $status,
                rawResponse: $response,
            );
        }

        $errorMessage = $response['errorMessage'] ?? 'Could not retrieve report';

        $this->logger->warning('Report check failed', [
            'error' => $errorMessage,
            'error_code' => $errorCode,
        ]);

        return ReportResponse::failure(
            errorMessage: $errorMessage,
            rawResponse: $response,
        );
    }
}
