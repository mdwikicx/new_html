<?php

namespace Html;
/*
use function Html\wiki_text_to_html;
*/

use function Post\handle_url_request;
// use function Post\post_url_params_result;
use function HtmlFixes\fix_link_red;
use function HtmlFixes\del_div_error;
use function NewHtml\FileHelps\file_write; // file_write($file_html, $result);
use function NewHtml\FileHelps\read_file;
use function Printn\test_print;

function change_it($text, $title)
{
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/Sandbox";

    $title2 = str_replace("/", "%2F", $title);
    // $title2 = str_replace(" ", "_", $title2);
    $url = "https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/$title2";

    $data = ['wikitext' => $text];
    // $response = post_url_params_result($url, $data);
    $response = handle_url_request($url, 'POST', $data);

    // Handle the response from your API
    if ($response === false) {
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

function do_wiki_text_to_html($wikitext, $title)
{
    // ---
    $title = str_replace(" ", "_", $title);
    // ---
    if ($wikitext == '') return "";
    // ---
    $fixed = change_it($wikitext, $title);
    // ---
    $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? '';
    // ---
    if ($result == '') return "";
    // ---
    $result = del_div_error($result);
    $result = fix_link_red($result);
    // ---
    return $result;
}

function wiki_text_to_html($wikitext, $file_html, $title)
{
    // ---
    $from_cache = false;
    // ---
    if (!isset($_GET['new'])) {
        // ---
        $text = read_file($file_html);
        // ---
        if ($text != '') return [$text, true];
    }
    // ---
    if ($wikitext == '') return ["", $from_cache];
    // ---
    $result = do_wiki_text_to_html($wikitext, $title);
    // ---
    if ($result == '') return ["", $from_cache];
    // ---
    file_write($file_html, $result);
    // ---
    return [$result, $from_cache];
}
