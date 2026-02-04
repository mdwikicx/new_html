<?php

namespace MDWiki\NewHtml\Services\Api;

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

function convert_wikitext_to_html(string $text, string $title): array
{
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/Sandbox";

    $title2 = str_replace("/", "%2F", $title);
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/$title2";

    $data = ['wikitext' => $text];
    $response = handle_url_request($url, 'POST', $data);

    if ($response === "") {
        test_print("API request failed: " . json_encode($data));
        return ['error' => 'Error: Could not reach API.'];
    }
    if (strpos($response, ">Wikimedia Error<") !== false) {
        test_print("API returned error: $response");
        return ['error' => 'Error: Wikipedia API returned an error.'];
    }
    if (empty($response)) {
        test_print("API returned empty response: " . json_encode($data));
        return ['error' => 'Error: Wikipedia API returned an empty response.'];
    }
    if (strpos($response, "<html") === false) {
        test_print("API returned invalid HTML: " . json_encode($data));
        return ['error' => 'Error: Wikipedia API returned invalid HTML.'];
    }

    return ['result' => $response];
}
