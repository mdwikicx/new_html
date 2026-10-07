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

    if (!empty($all)) $file_dir .= "_all";

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

    // Reject OPTIONS requests without origin
    if (!$origin) {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(400);
            exit;
        }
        return;
    }

    $origin_host = parse_url($origin, PHP_URL_HOST);

    // Reject unauthorized origins
    if (!in_array($origin_host, $allowed_domains, true)) {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(403);
            exit;
        }
        return;
    }

    // Set CORS headers for allowed origins only
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
