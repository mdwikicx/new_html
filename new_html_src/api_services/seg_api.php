<?php

namespace APIServices;

use function Printn\test_print;
use function APIServices\handle_url_request;
// use function APIServices\post_url_params_result;

function change_html_to_seg($text)
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
