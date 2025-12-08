<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Contracts;

/**
 * HTTP Client Interface
 * 
 * Abstraction for HTTP client implementations
 */
interface HttpClientInterface
{
    /**
     * Make a POST request
     * 
     * @param string $url The URL to send the request to
     * @param array<string, mixed> $data The data to send
     * @param array<string, string> $headers Additional headers
     * @return array<string, mixed> The response data
     * @throws \Sarkhanrasimoghlu\Lsim\Exceptions\HttpException
     */
    public function post(string $url, array $data = [], array $headers = []): array;

    /**
     * Make a GET request
     * 
     * @param string $url The URL to send the request to
     * @param array<string, string> $headers Additional headers
     * @return array<string, mixed> The response data
     * @throws \Sarkhanrasimoghlu\Lsim\Exceptions\HttpException
     */
    public function get(string $url, array $headers = []): array;
}