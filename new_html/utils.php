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
 * Set CORS headers for allowed domains
 *
 * Checks the Origin header against allowed domains and sets appropriate CORS headers.
 *
 * @return void
 */
function set_cors_headers(): void
{
    $allowed_domains = [
        'mdwikicx.toolforge.org',
        'mdwiki.toolforge.org',
        'medwiki.toolforge.org',
    ];

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if ($origin) {
        $origin_host = parse_url($origin, PHP_URL_HOST);

        if (in_array($origin_host, $allowed_domains, true)) {
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Allow-Credentials: true'); // إذا كنت تحتاج cookies
            header('Access-Control-Max-Age: 86400'); // cache لمدة 24 ساعة
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
/**
 * Get the content type based on printetxt parameter
 *
 * @param string $printetxt The output format (wikitext|html|seg)
 * @return string The content type (text/plain, text/html, or application/json)
 */
function get_content_type(string $printetxt): string
{
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
