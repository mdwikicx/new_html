<?php

/**
 * MDWiki API services
 *
 * Provides functions for fetching wikitext content from MDWiki
 * using both the API and REST API endpoints.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;
use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

/**
 * Service for fetching wikitext content from MDWiki
 * // https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
 * // https://mdwiki.org/w/rest.php/v1/revision/1420795/html
 *
 * @package MDWiki\NewHtml\Services\Api
 */
class MdwikiApiService
{
    private HttpClientInterface $httpClient;
    private string $baseApiUrl;
    private string $baseRestUrl;
    private ?string $localDirectoryOverride;

    /**
     * Constructor
     *
     * @param HttpClientInterface|null $httpClient HTTP client for making requests (uses HttpClientService if null)
     * @param string $baseApiUrl The base URL for the MDWiki API
     * @param string $baseRestUrl The base URL for the MDWiki REST API
     * @param string|null $localDirectory Optional local directory override for testing
     */
    public function __construct(
        ?HttpClientInterface $httpClient = null,
        string $baseApiUrl = 'https://mdwiki.org/w/api.php',
        string $baseRestUrl = 'https://mdwiki.org/w/rest.php/v1',
        ?string $localDirectory = null,
    ) {
        $this->httpClient = $httpClient ?? new HttpClientService();
        $this->baseApiUrl = $baseApiUrl;
        $this->baseRestUrl = $baseRestUrl;
        $this->localDirectoryOverride = $localDirectory;
    }

    /**
     * Get the local rtt_members directory path.
     *
     * @return string
     */
    private function getLocalDirectory(): string
    {
        if ($this->localDirectoryOverride !== null) {
            return $this->localDirectoryOverride;
        }

        $home = getenv('HOME');
        if (empty($home) && function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
            $pw = posix_getpwuid(posix_getuid());
            if (is_array($pw) && !empty($pw['dir'])) {
                $home = $pw['dir'];
            }
        }
        return rtrim((string)$home, '/\\') . '/data/rtt_members';
    }

    /**
     * Safely read content from local file if it exists within the rtt_members directory.
     *
     * @param string $filename Encoded filename
     * @return string|null File contents or null if invalid/missing
     */
    private function readLocalFile(string $filename): ?string
    {
        $dir = $this->getLocalDirectory();
        $baseDirReal = realpath($dir);
        if ($baseDirReal === false) {
            return null;
        }

        $filePath = $baseDirReal . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($filePath)) {
            return null;
        }

        $realPath = realpath($filePath);
        if ($realPath === false) {
            return null;
        }

        // Security check: ensure realPath starts with baseDirReal directory path
        if (!str_starts_with($realPath, $baseDirReal . DIRECTORY_SEPARATOR)) {
            return null;
        }

        $content = file_get_contents($realPath);
        return $content !== false ? $content : null;
    }

    /**
     * Get raw API response from MDWiki API for a given page title
     *
     * @param string $title The title of the page to fetch
     * @return array{error: string, httpCode: mixed, response: bool|string} The raw API response (JSON string) or error information
     */
    public function handleRawRequest(string $title): array
    {
        $params = [
            "action" => "query",
            "format" => "json",
            "prop" => "revisions",
            "titles" => $title,
            "utf8" => 1,
            "formatversion" => "2",
            "rvprop" => "content|ids"
        ];

        $response = $this->httpClient->handleRawRequest($this->baseApiUrl, 'GET', $params);
        return $response;
    }
    /**
     * Get wikitext content from MDWiki API
     *
     * @param string $title The title of the page to fetch
     * @return array{source: string, revid: string|int} Array containing source and revid
     */
    public function getWikitextFromMdwikiApi(string $title): array
    {
        $params = [
            "action" => "query",
            "format" => "json",
            "prop" => "revisions",
            "titles" => $title,
            "utf8" => 1,
            "formatversion" => "2",
            "rvprop" => "content|ids"
        ];

        $responseArray = $this->httpClient->request($this->baseApiUrl, 'GET', $params);
        $response = $responseArray['output'];

        if (empty($response)) {
            error_log("MdwikiApiService: Failed to fetch data from MDWiki API for title: $title");
            test_print("Failed to fetch data from MDWiki API for title: $title");
            return ['source' => '', 'revid' => ''];
        }

        $json = json_decode($response, true);
        $revisions = $json["query"]["pages"][0]["revisions"][0] ?? [];

        if (empty($revisions)) {
            error_log("MdwikiApiService: No revision data found for title: $title");
            test_print("No revision data found for title: $title");
            return ['source' => '', 'revid' => ''];
        }

        $source = $revisions["content"] ?? '';
        $revid = $revisions["revid"] ?? '';
        return [
            "source" => $source,
            "revid" => $revid,
        ];
    }

    /**
     * Convert page title to local JSON filename according to naming rules.
     *
     * @param string $title Page title
     * @return string Filename with .json extension
     */
    public function getFileNameFromTitle(string $title): string
    {
        return rawurlencode(str_replace(' ', '_', $title)) . '.json';
    }

    /**
     * Get wikitext content from MDWiki REST API
     *
     * @param string $title The title of the page to fetch
     * @return array{source: string, revid: string|int, error: string}
     */
    public function getWikitextFromMdwikiRestApi(string $title): array
    {
        $encodedTitle = rawurlencode(str_replace(' ', '_', $title));
        $filename = $encodedTitle . '.json';
        $url = "{$this->baseRestUrl}/page/{$encodedTitle}";

        $responseArray = $this->httpClient->request($url, 'GET', [], false, 3, 3);
        $response  = $responseArray['output'];
        $error     = $responseArray['error'];
        $errorCode = $responseArray['error_code'];
        $httpCode  = $responseArray['httpCode'] ?? 200;

        $failureReason = null;

        if ($httpCode !== 200) {
            $failureReason = 'http code';
        } elseif (empty($response)) {
            if ($errorCode === 'TIMEOUT' || $errorCode === 'CURL_ERROR' || (is_string($error) && str_contains(strtolower($error), 'timeout'))) {
                $failureReason = 'timeout';
            } else {
                $failureReason = 'http code';
            }
        } else {
            $json = json_decode($response, true);
            if (!is_array($json)) {
                $failureReason = 'json';
            }
        }

        if ($failureReason === null && isset($json) && is_array($json)) {
            $source = $json["source"] ?? '';
            $revid = $json["latest"]["id"] ?? '';

            return [
                "source" => $source,
                "revid" => $revid,
                "error" => $error,
            ];
        }

        error_log("MdwikiApiService: Falling back to local file for title '$title' due to $failureReason");

        $localContent = $this->readLocalFile($filename);
        if ($localContent !== null) {
            $localJson = json_decode($localContent, true);
            if (is_array($localJson)) {
                $source = $localJson["source"] ?? '';
                $revid = $localJson["latest"]["id"] ?? '';
                return [
                    "source" => $source,
                    "revid" => $revid,
                    "error" => "",
                ];
            }
        }

        error_log("MdwikiApiService: Failed to fetch data from MDWiki REST API for title: $title");
        test_print("Failed to fetch data from MDWiki REST API for title: $title");
        return ['source' => '', 'revid' => '', 'error' => $error];
    }
}
