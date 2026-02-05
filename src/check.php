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

require_once __DIR__ . "/bootstrap.php";

$revid = $_GET['revid'] ?? '';

if (empty($revid) || !ctype_digit($revid)) {
    echo 'false';
    exit;
}

$revisions_dir = __DIR__ . "/../../revisions_new";

if (strpos(__DIR__, 'public_html') !== false) {
    $home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');
    $revisions_dir = $home . "/public_html/revisions_new";
}

$dir_path = "$revisions_dir/$revid";

if (!is_dir($dir_path)) {
    echo 'false';
    exit;
}

$seg_exists = is_file("$dir_path/seg.html");
$html_exists = is_file("$dir_path/html.html");

$ex = $seg_exists && $html_exists;
echo $ex ? 'true' : 'false';
