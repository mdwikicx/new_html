<?php

/**
 * HTTP Client Service for making API requests
 *
 * Implements HttpClientInterface using cURL
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;
use function MDWiki\NewHtml\Services\Api\handle_url_request;

class HttpClientService implements HttpClientInterface
{
    /**
     * Send an HTTP request to the specified endpoint
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @return string The response body, or empty string on failure
     */
    public function request(string $endPoint, string $method = 'GET', array $params = []): string
    {
        return handle_url_request($endPoint, $method, $params);
    }
}
