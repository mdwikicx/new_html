<?php

namespace MDWiki\NewHtml\Services\Api;

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

function change_html_to_seg(string $text): array
{
    $url = 'https://ncc2c.toolforge.org/HtmltoSegments';

    $data = ['html' => $text];
    $response = handle_url_request($url, 'POST', $data);

    if ($response === "") {
        test_print("API request failed: " . json_encode($data));
        return ['error' => 'Error: Could not reach API.'];
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        return ['error' => 'Error: ' . $data['error']];
    }

    if (isset($data['result'])) {
        return ['result' => $data['result']];
    } else {
        return ['error' => 'Error: Unexpected response format.'];
    }
}
