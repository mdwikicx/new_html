<?php

declare (strict_types = 1);
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
    public const DEFAULT_USER_AGENT =
        'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.mdwikicx@toolforge.org)';

    private static ?self $instance = null;

    public function __construct(
        public readonly string $userAgent = self::DEFAULT_USER_AGENT,
        private readonly int $defaultTimeout = 15,
        private readonly int $defaultConnectTimeout = 5,
    ) {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Perform a raw cURL request.
     *
     * @param array<string, mixed> $params
     * @return array{printableUrl: string, httpCode: int, response: bool|string, error: string, errno: int}
     */
    public function handleRawRequest(
        string $endPoint,
        string $method = 'GET',
        array $params = [],
        bool $json = false,
        ?int $timeout = null,
        ?int $connectTimeout = null
    ): array {
        $method = strtoupper($method);
        $url    = $this->buildUrl($endPoint, $method, $params);

        $ch = curl_init($url);
        if ($ch === false) {
            return [
                'printableUrl' => $url,
                'httpCode'     => 0,
                'response'     => false,
                'error'        => 'curl_init failed',
                'errno'        => -1,
            ];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_COOKIEJAR => "cookie.txt",
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout ?? $this->defaultConnectTimeout,
            CURLOPT_TIMEOUT        => $timeout ?? $this->defaultTimeout,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($json) {
                $body = json_encode($params, JSON_THROW_ON_ERROR);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        }

        $output = curl_exec($ch);
        $result = [
            'printableUrl' => $url,
            'httpCode'     => (int) curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'response'     => $output,
            'error'        => curl_error($ch),
            'errno'        => curl_errno($ch),
        ];

        curl_close($ch);

        return $result;
    }

    /**
     * Perform a request and return a normalized result.
     *
     * @param array<string, mixed> $params
     * @return array{output: string, error_code: string, error: string, errno: int}
     */
    public function request(
        string $endPoint,
        string $method = 'GET',
        array $params = [],
        bool $json = false,
        ?int $timeout = null,
        ?int $connectTimeout = null
    ): array {
        $raw = $this->handleRawRequest($endPoint, $method, $params, $json, $timeout, $connectTimeout);

        $url      = $raw['printableUrl'];
        $httpCode = $raw['httpCode'];
        $output   = $raw['response'];

        Logger::debug($url);

        $result = [
            'output'     => '',
            'error_code' => '',
            'error'      => '',
            'errno'      => $raw['errno'],
        ];

        if ($output === false) {
            Logger::error("HttpClientService: cURL error for $url - {$raw['error']}");
            $result['error']      = $raw['error'];
            $result['error_code'] = 'CURL_ERROR';
            return $result;
        }

        if ($httpCode !== 200) {
            Logger::error("HttpClientService: HTTP $httpCode for URL: $url");
            $result['error']      = 'HTTP_ERROR';
            $result['error_code'] = (string) $httpCode;

            // Check for Cloudflare protection
            if ($this->isCloudflareChallenge($output)) {
                Logger::error("HttpClientService: Cloudflare protection detected for URL: $url");
                $result['error'] = 'CLOUDFLARE_PROTECTION';
            } else {
                Logger::debug(var_export($output, true));
            }

            return $result;
        }

        $result['output'] = $output;
        return $result;
    }

    /** @param array<string, mixed> $params */
    private function buildUrl(string $endPoint, string $method, array $params): string
    {
        if ($method !== 'GET' || $params === []) {
            return $endPoint;
        }
        return $endPoint . (str_contains($endPoint, '?') ? '&' : '?') . http_build_query($params);
    }

    private function isCloudflareChallenge(string $body): bool
    {
        return str_contains($body, 'Just a moment...');
    }
}
