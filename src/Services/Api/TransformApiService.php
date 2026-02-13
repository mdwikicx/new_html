<?php

/**
 * Wikipedia transform API services
 *
 * Provides functions for converting wikitext to HTML using the
 * Wikipedia REST API transform endpoint.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;
use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

/**
 * Service for converting wikitext to HTML using the Wikipedia REST API
 *
 * @package MDWiki\NewHtml\Services\Api
 */
class TransformApiService
{
    private HttpClientInterface $httpClient;
    private string $baseUrl;

    /**
     * Constructor
     *
     * @param HttpClientInterface|null $httpClient HTTP client for making requests (uses HttpClientService if null)
     * @param string $baseUrl The base URL for the Wikipedia REST API
     */
    public function __construct(
        ?HttpClientInterface $httpClient = null,
        string $baseUrl = 'https://en.wikipedia.org/w/rest.php/v1',
    ) {
        $this->httpClient = $httpClient ?? new HttpClientService();
        $this->baseUrl = $baseUrl;
    }

    /**
     * Convert wikitext to HTML using the Wikipedia REST API
     *
     * @param string $text The wikitext to convert
     * @param string $title The title of the page (used for context in conversion)
     * @return array<string, string> Array with 'result' key on success or 'error' key on failure
     */
    public function convertWikitextToHtml(string $text, string $title): array
    {
        $titleEncoded = str_replace("/", "%2F", $title);
        // $titleEncoded = str_replace(" ", "_", $titleEncoded);
        $url = "{$this->baseUrl}/transform/wikitext/to/html/{$titleEncoded}";

        $data = ['wikitext' => $text];
        $response = $this->httpClient->request($url, 'POST', $data);

        // Handle the response from the API
        if ($response === "") {
            test_print("API request failed: " . json_encode($data));
            return ['error' => 'Error: Could not reach API.'];
        }

        // Check if response contains an error
        if (strpos($response, ">Wikimedia Error<") !== false) {
            test_print("API returned error: $response");
            return ['error' => 'Error: Wikipedia API returned an error.'];
        }

        // Check if response is empty
        if (empty($response)) {
            test_print("API returned empty response: " . json_encode($data));
            return ['error' => 'Error: Wikipedia API returned an empty response.'];
        }

        // Check if response is valid HTML
        if (strpos($response, "<html") === false) {
            test_print("API returned invalid HTML: " . json_encode($data));
            return ['error' => 'Error: Wikipedia API returned invalid HTML.'];
        }

        return ['result' => $response];
    }
}

/**
 * Legacy function for backward compatibility
 *
 * Converts wikitext to HTML using the Wikipedia REST API.
 *
 * @param string $text The wikitext to convert
 * @param string $title The title of the page (used for context in conversion)
 * @return array<string, string> Array with 'result' key on success or 'error' key on failure
 */
function convertWikitextToHtml(string $text, string $title): array
{
    $service = new TransformApiService();
    return $service->convertWikitextToHtml($text, $title);
}
