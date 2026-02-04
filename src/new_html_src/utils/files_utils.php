<?php

/**
 * File I/O helper utilities
 *
 * Provides functions for file and directory operations, including
 * reading, writing, and managing the revisions storage directory.
 *
 * @package MDWiki\NewHtml
 */

namespace NewHtml\FileHelps;

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

use function Printn\test_print;

$revisions_new_dir = dirname(dirname(__DIR__)) . '/revisions_new';

if (strpos(__DIR__, 'public_html') !== false) {
    $revisions_new_dir = getenv('HOME') . "/public_html/revisions_new";
}
/**
 * Get the revisions directory path
 *
 * @return string The absolute path to the revisions directory
 */
function get_revisions_new_dir(): string
{
    global $revisions_new_dir;
    return $revisions_new_dir;
}

/**
 * Get the file directory for a specific revision
 *
 * @param string $revision The revision ID
 * @param string $all Whether to use the '_all' suffix (non-empty string) or not (empty string)
 * @return string The directory path, or empty string on error
 */
function get_file_dir(string $revision, string $all): string
{
    global $revisions_new_dir;

    if (empty($revision) || !ctype_digit($revision)) {
        test_print('Error: revision is empty in get_file_dir().');
        return '';
    }

    $file_dir = $revisions_new_dir . "/$revision";

    if ($all != '') $file_dir .= "_all";

    if (!is_dir($file_dir)) {
        if (!mkdir($file_dir, 0755, true)) {
            test_print(sprintf('Failed to create directory "%s".', $file_dir));
        }
    }
    return $file_dir;
}

/**
 * Write text to a file with locking
 *
 * @param string|null $file The file path to write to
 * @param string $text The content to write
 * @return void
 */
function file_write(?string $file, string $text): void
{
    if (empty($text) || empty($file)) {
        return;
    }

    try {
        file_put_contents($file, $text, LOCK_EX);
    } catch (\Exception $e) {
        test_print("Error: Could not write to file: $file");
    }
}

/**
 * Read the contents of a file
 *
 * @param string|null $file The file path to read from
 * @return bool|string The file contents, or empty string on error
 */
function read_file(?string $file): bool|string
{

    if (empty($file) || !file_exists($file)) {
        return "";
    }

    try {
        return file_get_contents($file);
    } catch (\Exception $e) {
        test_print("Error: Could not read file: $file");
    }

    return "";
}
