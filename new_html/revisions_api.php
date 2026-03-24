<?php

/**
 * Reisions dashboard page
 *
 * Displays all processed revisions in an HTML table with links to view
 * generated files (wikitext, HTML, segments). Also handles JSON cache
 * regeneration.
 *
 * @package MDWiki\NewHtml
 */
?>
<?php
define('DEBUGX', true); // Set APP_DEBUG=1 in development

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Use modern PSR-4 autoloading
require_once __DIR__ . "/bootstrap.php";

use function MDWiki\NewHtml\Infrastructure\Utils\read_file;

/**
 * Generate a badge indicating if a file exists in the list
 *
 * @param string[] $files Array of existing filenames
 * @param string $file The filename to check
 * @return string HTML badge markup, or empty string if file exists
 */
function make_badge(array $files, string $file): string
{

    if (!in_array($file, $files)) {
        return "<span class='badge bg-danger'>Missing</span>";
    }

    return "";
}

/**
 * Get data from JSON file based on type
 *
 * @param string $tyt The type of data to retrieve ('all' for complete data, otherwise main data)
 * @return array<string, mixed> The decoded JSON data as an array
 */
function get_Data(string $tyt): array
{
    $file = ($tyt == 'all') ? JSON_FILE_ALL : JSON_FILE;

    $file_text = read_file($file);

    if (empty($file_text)) return [];

    $data = json_decode($file_text, true) ?? [];
    return $data;
}

$dirs = array_filter(glob(REVISIONS_PATH . '/*/'), 'is_dir');
// sort directories by last modified date
usort($dirs, function ($a, $b) {
    $timeA = is_file($a . '/wikitext.txt') ? filemtime($a . '/wikitext.txt') : filemtime($a);
    $timeB = is_file($b . '/wikitext.txt') ? filemtime($b . '/wikitext.txt') : filemtime($b);
    return $timeB - $timeA;
});

$tbody = '';
$number = 0;
$main_data = get_Data('');
$main_data_all = get_Data('all');

$make_dump = empty($main_data);

foreach ($dirs as $dir) {

    $number += 1;

    $wikitextFile = $dir . '/wikitext.txt';
    $lastModified = is_file($wikitextFile)
        ? date('Y-m-d H:i', filemtime($wikitextFile))
        : date('Y-m-d H:i', filemtime($dir));

    $dir = rtrim($dir, '/');

    $dir_path = basename($dir);
    $oldid_number = str_replace('_all', '', $dir_path);

    $files = array_filter(glob("$dir/*"), 'is_file');

    $files = array_map('basename', $files);

    // if wikitext.txt in $files
    $wikitext_tag = make_badge($files, 'wikitext.txt');
    $html_tag = make_badge($files, 'html.html');
    $seg_tag = make_badge($files, 'seg.html');

    $title = (is_file("$dir/title.txt")) ? file_get_contents("$dir/title.txt") : '';

    $title = str_replace('_', ' ', $title);

    if (!empty($title) && $make_dump && !empty($oldid_number)) {
        $id = (int)$oldid_number;
        if ($id > 0) {
            if (strpos($dir_path, '_all') !== false) {
                $main_data_all[$title] = $id;
            } else {
                $main_data[$title] = $id;
            }
        }
    }

    $title = htmlspecialchars($title);

    $url = "open.php?revid=$dir_path&file";

    $re_create_td = (isset($_GET['re'])) ? <<<HTML
        <td>
            <a class="card-link" href="/new_html/index.php?new=1&title=$title" target="_blank">Re create</a>
        </td>
    HTML : "";

    $tbody .= <<<HTML
        <tr>
            <td>$number</td>
            <td>$lastModified</td>
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?title=$title" target="_blank">$title</a>
            </td>
            $re_create_td
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?oldid=$oldid_number" target="_blank">$dir_path</a>
            </td>
            <td>
                <a class="card-link" href="$url=wikitext.txt" target="_blank">Wikitext</a> $wikitext_tag
            </td>
            <td>
                <a class="card-link" href="$url=html.html" target="_blank">Html</a> $html_tag
            </td>
            <td>
                <a class="card-link" href="$url=seg.html" target="_blank">Segments</a> $seg_tag
            </td>
        </tr>
    HTML;
}

function file_write(?string $file, string $text): void
{
    if (empty($text) || empty($file)) {
        return;
    }

    try {
        file_put_contents($file, $text, LOCK_EX);
    } catch (\Exception $e) {
        error_log("Error: Could not write to file: $file");
    }
}

if ($make_dump) {
    file_write(JSON_FILE, json_encode($main_data, JSON_PRETTY_PRINT));
    file_write(JSON_FILE_ALL, json_encode($main_data_all, JSON_PRETTY_PRINT));
}
