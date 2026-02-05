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

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;
use function MDWiki\NewHtml\Services\Api\handle_url_request;
// use function MDWiki\NewHtml\Services\Api\post_url_params_result;

/**
 * Convert HTML to segments using the HtmltoSegments API
 *
 * @param string $text The HTML text to convert to segments
 * @return array<string, string> Array with 'result' key on success or 'error' key on failure
 */
function change_html_to_seg(string $text): array
{
    $url = 'https://ncc2c.toolforge.org/HtmltoSegments';

    $data = ['html' => $text];
    // $response = post_url_params_result($url, $data);
    $response = handle_url_request($url, 'POST', $data);

    // Handle the response from your API
    if ($response === "") {
        test_print("API request failed: " . json_encode($data));
        return ['error' => 'Error: Could not reach API.'];
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        return ['error' => 'Error: ' . $data['error']];
    }

    // Extract the result from the API response
    if (isset($data['result'])) {
        return ['result' => $data['result']];
    } else {
        return ['error' => 'Error: Unexpected response format.'];
    }
}
