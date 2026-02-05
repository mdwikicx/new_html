<?php
/**
 * Cache existence checker for generated content
 *
 * Checks if both seg.html and html.html files exist for a given revision.
 * Returns 'true' if both files exist, 'false' otherwise.
 *
 * Request parameters:
 * - revid: Revision ID (must be numeric)
 *
 * @package MDWiki\NewHtml
 */

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$revid = $_GET['revid'] ?? '';

if (empty($revid) || !ctype_digit($revid)) {
    echo 'false';
    exit;
}

$dir_path = __DIR__ . "/../../revisions_new/$revid";

if (strpos(__DIR__, 'public_html') !== false) {
    // dir_path = $HOME / public_html/revisions_new
    $dir_path = getenv('HOME') . "/public_html/revisions_new/$revid";
}
if (!is_dir($dir_path)) {
    echo 'false';
    exit;
}

$seg_exists = is_file("$dir_path/seg.html");
$html_exists = is_file("$dir_path/html.html");

$ex = $seg_exists && $html_exists;
echo $ex ? 'true' : 'false';
