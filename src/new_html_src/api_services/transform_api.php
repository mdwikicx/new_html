<?php

/**
 * Wikipedia transform API services
 *
 * Provides functions for converting wikitext to HTML using the
 * Wikipedia REST API transform endpoint.
 *
 * @package MDWiki\NewHtml\APIServices
 */

namespace APIServices;

use function MDWiki\NewHtml\APIServices\handle_url_request;
// use function MDWiki\NewHtml\APIServices\post_url_params_result;
use function Printn\test_print;

/**
 * Convert wikitext to HTML using the Wikipedia REST API
 *
 * @param string $text The wikitext to convert
 * @param string $title The title of the page (used for context in conversion)
 * @return array<string, string> Array with 'result' key on success or 'error' key on failure
 */
function convert_wikitext_to_html(string $text, string $title): array
{
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/Sandbox";

    $title2 = str_replace("/", "%2F", $title);
    // $title2 = str_replace(" ", "_", $title2);
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/$title2";

    $data = ['wikitext' => $text];
    // $response = post_url_params_result($url, $data);
    $response = handle_url_request($url, 'POST', $data);

    // Handle the response from your API
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
