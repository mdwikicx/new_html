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
use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

class HttpClientService implements HttpClientInterface
{
    /**
     * Handle a raw HTTP request using cURL
     *
     * @param string $endPoint
     * @param string $method
     * @param array<string, mixed> $params
     * @return array{printableUrl: string, httpCode: int, response: bool|string, error: string}
     */
    public function handleRawRequest(
        string $endPoint,
        string $method = 'GET',
        array $params = [],
    ): array {
        $ch = curl_init();
        $user_agent = defined('USER_AGENT') ? USER_AGENT : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

        $printableUrl = $endPoint;

        // POST with parameters should not have the parameters in the URL
        // GET with parameters should have the parameters in the URL
        if (!empty($params) && $method === 'GET') {
            $queryString = http_build_query($params);
            $printableUrl = strpos($printableUrl, '?') === false
                ? "$printableUrl?$queryString"
                : "$printableUrl&$queryString";
            $endPoint = $printableUrl;
        }

        curl_setopt($ch, CURLOPT_URL, $endPoint);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $output = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [
            'printableUrl' => $printableUrl,
            'httpCode' => $httpCode,
            'response' => $output,
            'error' => curl_error($ch)
        ];
    }
    /**
     * Handle URL requests with support for GET and POST methods
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @return string The response body, or empty string on failure
     */
    public function request(string $endPoint, string $method = 'GET', array $params = []): string
    {
        $rawResponse = $this->handleRawRequest($endPoint, $method, $params);

        $printableUrl = $rawResponse['printableUrl'];
        $httpCode = $rawResponse['httpCode'];
        $output = $rawResponse['response'];
        $error = $rawResponse['error'];

        test_print($printableUrl);

        if ($output === false) {
            test_print("endPoint: ($endPoint), cURL Error: " . $error);
            return '';
        }

        if ($httpCode !== 200) {
            test_print("API returned HTTP $httpCode: $httpCode");
            test_print(var_export($output, true));
            $output = '';
        }

        return $output;
    }
}

/**
 * Legacy function for backward compatibility
 * Handle URL requests with support for GET and POST methods
 *
 * @param string $endPoint The API endpoint URL
 * @param string $method The HTTP method to use ('GET' or 'POST')
 * @param array<string, mixed> $params Optional parameters to send with the request
 * @return string The response body, or empty string on failure
 */
function handle_url_request(string $endPoint, string $method = 'GET', array $params = []): string
{
    $service = new HttpClientService();
    return $service->request($endPoint, $method, $params);
}
