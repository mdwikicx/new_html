<?php

namespace MDWiki\NewHtmlMain\Utils;

/**
 * Get the file directory for a specific revision
 *
 * @param string $revision The revision ID
 * @param string $all Whether to use the '_all' suffix (non-empty string) or not (empty string)
 * @return string The directory path, or empty string on error
 */
function get_file_dir(string $revision, string $all): string
{
    if (empty($revision) || !ctype_digit($revision)) {
        error_log('Error: revision is empty in get_file_dir().');
        return '';
    }

    $file_dir = REVISIONS_PATH . "/$revision";

    if ($all != '') $file_dir .= "_all";

    if (!is_dir($file_dir)) {
        if (!mkdir($file_dir, 0755, true)) {
            error_log(sprintf('Failed to create directory "%s".', $file_dir));
        }
    }
    return $file_dir;
}

/**
 * Get the content type based on printetxt parameter
 *
 * @return string The content type (text/plain, text/html, or application/json)
 */
function get_content_type(): string
{
    $printetxt = $_GET['printetxt'] ?? $_GET['print'] ?? '';

    $content_types = [
        "wikitext" => "text/plain",
        "html" => "text/html",
        "seg" => "text/html",
    ];

    return $content_types[$printetxt] ?? "application/json";
}

/**
 * Generate error response for missing content
 *
 * Sends HTTP 404 status code and returns JSON error response.
 *
 * @param string $title The page title that was not found
 * @param string $revision The revision ID that was not found
 * @return string JSON encoded error response
 */
function error_1(string $title, string $revision): string
{
    // send request error code using http_response_code
    http_response_code(404);

    $data = [
        "sourceLanguage" => "en",
        "title" => $title,
        "revision" => $revision,
        "segmentedContent" => "",
        "categories" => [],
        "error_type" => "title:($title) or revision:($revision) not found",
        "error" => "No content found!",
    ];
    return json_encode($data);
}
