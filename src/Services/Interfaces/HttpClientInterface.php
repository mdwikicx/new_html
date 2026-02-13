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
    public function request(string $endPoint, string $method = 'GET', array $params = []): string;

    /**
     * Handle URL requests with support for GET and POST methods, returning detailed response information
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @return array{printableUrl: string, httpCode: int, response: bool|string, error: string} Detailed response information including the printable URL, HTTP status code, response body, and any error message
     */
    public function handleRawRequest(string $endPoint, string $method = 'GET', array $params = []): array;
}
