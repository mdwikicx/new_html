<?php

/**
 * JSON data storage for title-revision mappings
 *
 * Provides functions for managing JSON files that store mappings
 * between page titles and revision IDs, supporting both main and
 * 'all' data sets.
 *
 * @package MDWiki\NewHtml
 */

namespace MDWiki\NewHtml\Application\Controllers;

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;


/**
 * Get the revision ID for a specific title
 *
 * @param string $title The page title to look up
 * @param string $file The JSON file to search
 * @return string The revision ID if found, empty string otherwise
 */
function get_title_revision(string $title, string $file): string
{

    $file_text = read_file($file);

    if ($file_text == '') return '';

    $data = json_decode($file_text, true);

    if (!is_array($data)) return '';

    if (array_key_exists($title, $data)) {
        return $data[$title];
    }
    return "";
}

/**
 * Add or update a title-revision pair in the JSON data
 *
 * @param string $title The page title
 * @param string $revision The revision ID
 * @param string $file The JSON file to update
 * @return array<string, mixed>|string The updated data array on success, empty string on failure
 */
function add_title_revision(string $title, string $revision, string $file): array|string
{
    if (empty($title) || empty($revision)) return '';

    $file_text = read_file($file);

    if ($file_text == '') return '';

    $data = json_decode($file_text, true);

    if (!is_array($data)) return '';

    $data[$title] = $revision;

    file_write($file, json_encode($data));
    return $data;
}
