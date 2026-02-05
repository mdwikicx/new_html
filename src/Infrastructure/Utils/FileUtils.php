<?php

/**
 * File I/O helper utilities
 *
 * Provides functions for file and directory operations, including
 * reading, writing, and managing the revisions storage directory.
 *
 * @package MDWiki\NewHtml\Infrastructure\Utils
 */

namespace MDWiki\NewHtml\Infrastructure\Utils;

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
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
