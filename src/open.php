<?php

/**
 * File viewer for generated content
 *
 * Serves generated files (wikitext, HTML, segments) for a given revision.
 * Validates inputs to prevent path traversal attacks.
 *
 * Request parameters:
 * - revid: Revision ID (must be numeric)
 * - file: File to view (wikitext.txt|html.html|seg.html)
 *
 * @package MDWiki\NewHtml
 */

require_once __DIR__ . "/bootstrap.php";

use function HtmlFixes\remove_data_parsoid;

$revid = $_GET['revid'] ?? '';
$file = $_GET['file'] ?? '';

// Validate inputs to prevent path traversal
// revid can be like 1234_all or 1234
if (!preg_match('/^\d+(_all)?$/', $revid)) {
    http_response_code(400);
    echo "Invalid revision ID";
    exit();
}

$allowed_files = ['wikitext.txt', 'seg.html', 'html.html'];

if (!in_array($file, $allowed_files, true)) {
    http_response_code(400);
    echo "Invalid file parameter";
    exit();
}

$content_type = ($file == 'wikitext.txt') ? "text/plain" : "text/html";

header("Content-type: $content_type");

$file_path = REVISIONS_PATH . "/$revid/$file";

$text = file_get_contents($file_path) ?: '';

if (!empty($text)) {
    if ($file == "seg.html" || $file == "html.html") {
        $text = remove_data_parsoid($text);
    }
}

echo $text;
