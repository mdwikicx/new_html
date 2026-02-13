<?php

/**
 * Wikimedia Commons Image Service
 *
 * Provides functionality for checking image existence on Wikimedia Commons.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Services\Interfaces\CommonsImageServiceInterface;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class CommonsImageService implements CommonsImageServiceInterface
{
    private HttpClientInterface $httpClient;

    /**
     * Constructor
     *
     * @param HttpClientInterface|null $httpClient HTTP client for making requests
     */
    public function __construct(?HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new HttpClientService();
    }

    /**
     * Check if an image exists on Wikimedia Commons
     *
     * @param string $filename The filename to check (without File: prefix)
     * @return bool True if the image exists, false otherwise
     */
    public function imageExists(string $filename): bool
    {
        // Handle empty filenames
        if (empty(trim($filename))) {
            return false;
        }

        // Remove File: or Image: prefix if present
        $filename = preg_replace('/^(File|Image):/i', '', $filename);
        $filename = trim($filename);

        if (empty($filename)) {
            return false;
        }

        $params = [
            'action' => 'query',
            'titles' => 'File:' . $filename,
            'format' => 'json'
        ];

        $url = "https://commons.wikimedia.org/w/api.php";

        $response = $this->httpClient->request($url, 'GET', $params);

        // Handle the response from the API
        if ($response === "") {
            return true; // Assume exists on API failure
        }

        $json = json_decode($response, true);
        foreach ($json['query']['pages'] ?? [] as $page) {
            return !isset($page['missing']);
        }
        return false;
    }
}

/**
 * Legacy function for backward compatibility
 *
 * @param string $filename The filename to check (without File: prefix)
 * @return bool True if the image exists, false otherwise
 */
function check_commons_image_exists(string $filename): bool
{
    $service = new CommonsImageService();
    return $service->imageExists($filename);
}
