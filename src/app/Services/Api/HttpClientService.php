<?php

/**
 * HTTP Client Service for making API requests
 *
 * Implements HttpClientInterface using cURL
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class HttpClientService implements HttpClientInterface
{
    public string $userAgent;
    private static ?self $instance = null;

    public function __construct()
    {
        $this->userAgent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.mdwikicx@toolforge.org)';
    }

    public static function getInstance(): self
    {
        // used: Settings::getInstance()
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
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
        bool $json = false
    ): array {
        $ch         = curl_init();

        $printableUrl = $endPoint;

        // POST with parameters should not have the parameters in the URL
        // GET with parameters should have the parameters in the URL
        if (! empty($params) && $method === 'GET') {
            $queryString  = http_build_query($params);
            $printableUrl = strpos($printableUrl, '?') === false
                ? "$printableUrl?$queryString"
                : "$printableUrl&$queryString";
            $endPoint = $printableUrl;
        }

        curl_setopt($ch, CURLOPT_URL, $endPoint);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($json) {
                $jsonBody = json_encode($params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonBody),
                ]);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $output = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [
            'printableUrl' => $printableUrl,
            'httpCode'     => $httpCode,
            'response'     => $output,
            'error'        => curl_error($ch),
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
    public function request_string(string $endPoint, string $method = 'GET', array $params = [], bool $json = false): string
    {
        $rawResponse = $this->handleRawRequest($endPoint, $method, $params, $json);

        $printableUrl = $rawResponse['printableUrl'];
        $httpCode     = $rawResponse['httpCode'];
        $output       = $rawResponse['response'];
        $error        = $rawResponse['error'];

        Logger::debug($printableUrl);

        if ($output === false) {
            Logger::error("HttpClientService: cURL error for endPoint: $endPoint - " . $error);
            Logger::debug("endPoint: ($endPoint), cURL Error: " . $error);
            return '';
        }

        if ($httpCode !== 200) {
            Logger::error("HttpClientService: API returned HTTP $httpCode for URL: $printableUrl");

            // Check for Cloudflare protection
            $isCloudflareProtected = false;
            if (is_string($output) && str_contains($output, 'Just a moment...')) {
                $isCloudflareProtected = true;
                Logger::error("HttpClientService: Cloudflare protection detected for URL: $printableUrl");
                Logger::debug("Cloudflare protection detected: 'Just a moment...' page returned");
            }

            Logger::debug("API returned HTTP $httpCode: $httpCode");
            if (! $isCloudflareProtected) {
                Logger::debug(var_export($output, true));
            }
            $output = '';
        }

        return $output;
    }
    /**
     * Handle URL requests with support for GET and POST methods
     *
     * @param string $endPoint The API endpoint URL
     * @param string $method The HTTP method to use ('GET' or 'POST')
     * @param array<string, mixed> $params Optional parameters to send with the request
     * @param bool $json Whether to send the request as JSON
     * @return array{output: string, error_code: string, error: string}
     */
    public function request(string $endPoint, string $method = 'GET', array $params = [], bool $json = false): array
    {
        $rawResponse = $this->handleRawRequest($endPoint, $method, $params, $json);

        $printableUrl = $rawResponse['printableUrl'];
        $httpCode     = $rawResponse['httpCode'];
        $output       = $rawResponse['response'];
        $error        = $rawResponse['error'];

        Logger::debug($printableUrl);

        $result = [
            "output"     => "",
            "error_code" => "",
            "error"      => "",
        ];

        if ($output === false) {
            $result["error"]      = $error;
            $result["error_code"] = "CURL_ERROR";
            Logger::error("HttpClientService: cURL error for endPoint: $endPoint - " . $error);
            Logger::debug("endPoint: ($endPoint), cURL Error: " . $error);
            return $result;
        }
        $result["output"] = $output;

        if ($httpCode !== 200) {
            Logger::error("HttpClientService: API returned HTTP $httpCode for URL: $printableUrl");
            $result["error"]      = "HTTP_ERROR";
            $result["error_code"] = "$httpCode";

            // Check for Cloudflare protection
            $isCloudflareProtected = false;
            if (is_string($output) && str_contains($output, 'Just a moment...')) {
                $isCloudflareProtected = true;
                Logger::error("HttpClientService: Cloudflare protection detected for URL: $printableUrl");
                Logger::debug("Cloudflare protection detected: 'Just a moment...' page returned");
                $result["error"] = "CLOUDFLARE_PROTECTION";
            }

            Logger::debug("API returned HTTP $httpCode: $httpCode");
            if (! $isCloudflareProtected) {
                Logger::debug(var_export($output, true));
            }

            $result["output"] = '';
        }

        return $result;
    }
}
