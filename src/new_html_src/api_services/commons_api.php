<?php

/**
 * Wikimedia Commons API services
 *
 * Provides functions for checking image existence on Wikimedia Commons.
 *
 * @package MDWiki\NewHtml\APIServices
 */

namespace MDWiki\NewHtml\APIServices;

use function MDWiki\NewHtml\APIServices\handle_url_request;

/**
 * Check if an image exists on Wikimedia Commons
 *
 * @param string $filename The filename to check (without File: prefix)
 * @return bool True if the image exists, false otherwise
 */

function check_commons_image_exists(string $filename): bool
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

    $response = handle_url_request($url, 'GET', $params);

    // Handle the response from your API
    if ($response === "") {
        return true; // Assume exists on API failure
    }

    $json = json_decode($response, true);
    foreach ($json['query']['pages'] ?? [] as $page) {
        return !isset($page['missing']);
    }
    return false;
}
