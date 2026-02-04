<?php

namespace Segments;
/*
use function Segments\html_to_seg;
*/
use function Printn\test_print;
use function Post\handle_url_request;
use function NewHtml\FileHelps\file_write;
use function NewHtml\FileHelps\read_file;
// use function Post\post_url_params_result;

function change_html_to_seg($text)
{
    $url = 'https://ncc2c.toolforge.org/HtmltoSegments';

    $data = ['html' => $text];
    // $response = post_url_params_result($url, $data);
    $response = handle_url_request($url, 'POST', $data);

    // Handle the response from your API
    if ($response === false) {
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

function do_html_to_seg($text)
{
    // ---
    $fixed = change_html_to_seg($text);
    // ---
    // $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? "";
    // ---
    // $result = str_replace("https://medwiki.toolforge.org/md/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/w/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/wiki/", "https://en.wikipedia.org/wiki/", $result);
    // ---
    if ($result == 'Content for translate is not given or is empty') return "";
    // ---
    return $result;
}

function html_to_seg($text, $file_seg)
{
    // ---
    $from_cache = false;
    // ---
    if (!isset($_GET['new'])) {
        $seg_text = read_file($file_seg);
        // ---
        if ($seg_text != '') {
            return [$seg_text, true];
        }
    }
    // ---
    $result = do_html_to_seg($text);
    // ---
    if ($result == '') return ["", $from_cache];
    // ---
    file_write($file_seg, $result);
    // ---
    return [$result, $from_cache];
}
