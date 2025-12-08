<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\HttpClientInterface;
use Sarkhanrasimoghlu\Lsim\Exceptions\HttpException;

/**
 * Guzzle HTTP Client Implementation
 * 
 * HTTP client implementation using Guzzle
 */
final class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * Create a new Guzzle HTTP client
     * 
     * @param Client $client The Guzzle client instance
     * @param int $timeout Default timeout in seconds
     * @param bool $verifySSL Whether to verify SSL certificates
     */
    public function __construct(
        private readonly Client $client,
        private readonly int $timeout = 30,
        private readonly bool $verifySSL = true
    ) {}

    public function post(string $url, array $data = [], array $headers = []): array
    {
        return $this->makeRequest('POST', $url, ['form_params' => $data, 'headers' => $headers]);
    }

    public function get(string $url, array $headers = []): array
    {
        return $this->makeRequest('GET', $url, ['headers' => $headers]);
    }

    private function makeRequest(string $method, string $url, array $options = []): array
    {
        try {
            $options = array_merge($options, [
                'timeout' => $this->timeout,
                'verify' => $this->verifySSL,
            ]);

            $response = $this->client->request($method, $url, $options);
            return $this->parseResponse($response);
        } catch (\Exception $e) {
            throw $this->handleException($e, $url);
        }
    }

    private function handleException(\Exception $e, string $url): HttpException
    {
        return match (true) {
            $e instanceof ConnectException => HttpException::connectionFailed($url, $e->getMessage()),
            $e instanceof ClientException || $e instanceof ServerException => HttpException::httpError($e->getResponse()->getStatusCode(), $url),
            $e instanceof RequestException && str_contains($e->getMessage(), 'timeout') => HttpException::timeout($this->timeout),
            $e instanceof RequestException => HttpException::connectionFailed($url, $e->getMessage()),
            default => HttpException::connectionFailed($url, $e->getMessage())
        };
    }

    /**
     * Parse HTTP response
     * 
     * @param ResponseInterface $response The HTTP response
     * @return array<string, mixed> Parsed response data
     * @throws HttpException
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();
        
        if (empty($body)) {
            throw HttpException::invalidResponse('Empty response body');
        }

        $contentType = $response->getHeaderLine('Content-Type');
        
        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw HttpException::invalidResponse('Invalid JSON response: ' . json_last_error_msg());
            }
            
            return $decoded;
        }

        // For non-JSON responses, return as text
        return ['response' => $body];
    }
}