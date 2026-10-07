<?php

/**
 * HTTP Client Interface for API requests
 *
 * @package MDWiki\NewHtml\Services\Interfaces
 */

namespace MDWiki\NewHtml\Services\Interfaces;

interface HttpClientInterface
{
    /**
     * Send an HTTP request to the specified endpoint
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @return string The response body, or empty string on failure
     */
    public function request_string(string $endPoint, string $method = 'GET', array $params = [], bool $json = false): string;

    /**
     * Send an HTTP request to the specified endpoint
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @param bool $json Whether to send the request as JSON
     * @param int $timeout Timeout in seconds
     * @param int $connectTimeout Connection timeout in seconds
     * @return array{output: string, error_code: string, error: string, httpCode?: int}
     */
    public function request(string $endPoint, string $method = 'GET', array $params = [], bool $json = false, int $timeout = 15, int $connectTimeout = 5): array;

    /**
     * Handle URL requests with support for GET and POST methods, returning detailed response information
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @param bool $json Whether to send the request as JSON
     * @param int $timeout Timeout in seconds
     * @param int $connectTimeout Connection timeout in seconds
     * @return array{printableUrl: string, httpCode: int, response: bool|string, error: string, errno: int} Detailed response information
     */
    public function handleRawRequest(string $endPoint, string $method = 'GET', array $params = [], bool $json = false, int $timeout = 15, int $connectTimeout = 5): array;
}
