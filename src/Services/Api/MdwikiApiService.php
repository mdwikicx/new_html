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

    /**
     * Constructor
     *
     * @param HttpClientInterface|null $httpClient HTTP client for making requests (uses HttpClientService if null)
     * @param string $baseApiUrl The base URL for the MDWiki API
     * @param string $baseRestUrl The base URL for the MDWiki REST API
     */
    public function __construct(
        ?HttpClientInterface $httpClient = null,
        string $baseApiUrl = 'https://mdwiki.org/w/api.php',
        string $baseRestUrl = 'https://mdwiki.org/w/rest.php/v1',
    ) {
        $this->httpClient = $httpClient ?? new HttpClientService();
        $this->baseApiUrl = $baseApiUrl;
        $this->baseRestUrl = $baseRestUrl;
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
     * @return array{string, string}
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

        $response = $this->httpClient->request($this->baseApiUrl, 'GET', $params);

        if (empty($response)) {
            test_print("Failed to fetch data from MDWiki API for title: $title");
            return ['', ''];
        }

        $json = json_decode($response, true);
        $revisions = $json["query"]["pages"][0]["revisions"][0] ?? [];

        if (empty($revisions)) {
            test_print("No revision data found for title: $title");
            return ['', ''];
        }

        $source = $revisions["content"] ?? '';
        $revid = $revisions["revid"] ?? '';
        return [$source, $revid];
    }

    /**
     * Get wikitext content from MDWiki REST API
     *
     * @param string $title The title of the page to fetch
     * @return array{0: string, 1: string|int} Array containing [content, revision_id]
     */
    public function getWikitextFromMdwikiRestApi(string $title): array
    {
        $titleEncoded = str_replace("/", "%2F", $title);
        $titleEncoded = str_replace(" ", "_", $titleEncoded);
        $url = "{$this->baseRestUrl}/page/{$titleEncoded}";

        $response = $this->httpClient->request($url, 'GET');
        $json = json_decode($response, true);

        $source = $json["source"] ?? '';
        $revid = $json["latest"]["id"] ?? '';

        return [$source, $revid];
    }
}

/**
 * Legacy function for backward compatibility
 * Get wikitext content from MDWiki API
 *
 * @param string $title The title of the page to fetch
 * @return array{0: string, 1: string|int} Array containing [content, revision_id]
 */
function getWikitextFromMdwikiApi(string $title): array
{
    $service = new MdwikiApiService();
    return $service->getWikitextFromMdwikiApi($title);
}

/**
 * Legacy function for backward compatibility
 * Get wikitext content from MDWiki REST API
 *
 * @param string $title The title of the page to fetch
 * @return array{0: string, 1: string|int} Array containing [content, revision_id]
 */
function getWikitextFromMdwikiRestApi(string $title): array
{
    $service = new MdwikiApiService();
    return $service->getWikitextFromMdwikiRestApi($title);
}
/**
 * Legacy function for backward compatibility
 * Get raw API response from MDWiki API for a given page title
 *
 * @param string $title The title of the page to fetch
 * @return array{error: string, httpCode: mixed, response: bool|string} The raw API response (JSON string) or error information
 */
function handleRawRequest(string $title): array
{
    $service = new MdwikiApiService();
    return $service->handleRawRequest($title);
}
