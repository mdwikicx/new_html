<?php

/**
 * HtmltoSegments API services
 *
 * Provides functions for converting HTML to segmented content using
 * the HtmltoSegments API tool.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;
use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

/**
 * Service for converting HTML to segments using the HtmltoSegments API
 *
 * @package MDWiki\NewHtml\Services\Api
 */
class SegmentApiService
{
    private HttpClientInterface $httpClient;
    private string $apiUrl;

    /**
     * Constructor
     *
     * @param HttpClientInterface|null $httpClient HTTP client for making requests (uses HttpClientService if null)
     * @param string $apiUrl The API endpoint URL
     */
    public function __construct(
        ?HttpClientInterface $httpClient = null,
        string $apiUrl = 'https://ncc2c.toolforge.org/HtmltoSegments',
    ) {
        $this->httpClient = $httpClient ?? new HttpClientService();
        $this->apiUrl = $apiUrl;
    }

    /**
     * Convert HTML to segments
     *
     * @param string $html The HTML text to convert to segments
     * @return array<string, string> Array with 'result' key on success or 'error' key on failure
     */
    public function changeHtmlToSeg(string $html): array
    {
        $data = ['html' => $html];
        $response = $this->httpClient->request($this->apiUrl, 'POST', $data);

        // Handle the response from the API
        if ($response === "") {
            test_print("API request failed: " . json_encode($data));
            return ['error' => 'Error: Could not reach API.'];
        }

        $responseData = json_decode($response, true);
        if (isset($responseData['error'])) {
            return ['error' => 'Error: ' . $responseData['error']];
        }

        // Extract the result from the API response
        if (isset($responseData['result'])) {
            return ['result' => $responseData['result']];
        } else {
            return ['error' => 'Error: Unexpected response format.'];
        }
    }
}

/**
 * Legacy function for backward compatibility
 *
 * Converts HTML to segments using the default API endpoint.
 *
 * @param string $text The HTML text to convert to segments
 * @return array<string, string> Array with 'result' key on success or 'error' key on failure
 */
function changeHtmlToSeg(string $text): array
{
    $service = new SegmentApiService();
    return $service->changeHtmlToSeg($text);
}
