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

namespace NewHtml\JsonData;

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/*
use:
use function NewHtml\JsonData\get_title_revision;
use function NewHtml\JsonData\add_title_revision;
use function NewHtml\JsonData\get_from_json;
use function NewHtml\JsonData\get_Data;
use function NewHtml\JsonData\dump_both_data;
*/

use function NewHtml\FileHelps\get_file_dir;
use function NewHtml\FileHelps\file_write;
use function NewHtml\FileHelps\read_file;
use function NewHtml\FileHelps\get_revisions_new_dir;

$dir_path = get_revisions_new_dir();

/**
 * Dump both main data and all data to JSON files
 *
 * @param array<string, mixed> $main_data The main data to write
 * @param array<string, mixed> $main_data_all The complete data to write
 * @return void
 */
function dump_both_data(array $main_data, array $main_data_all): void
{

    file_write(JSON_FILE ?? '', json_encode($main_data, JSON_PRETTY_PRINT));
    file_write(JSON_FILE_ALL ?? '', json_encode($main_data_all, JSON_PRETTY_PRINT));
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

    if ($file_text == '') return [];

    $data = json_decode($file_text, true) ?? [];
    return $data;
}

/**
 * Get the revision ID for a specific title
 *
 * @param string $title The page title to look up
 * @param string $all Whether to use the 'all' data file (non-empty string) or main file (empty string)
 * @return string The revision ID if found, empty string otherwise
 */
function get_title_revision(string $title, string $all): string
{

    $file = (!empty($all)) ? JSON_FILE_ALL : JSON_FILE;

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
 * @param string $all Whether to use the 'all' data file (non-empty string) or main file (empty string)
 * @return array<string, mixed>|string The updated data array on success, empty string on failure
 */
function add_title_revision(string $title, string $revision, string $all): array|string
{
    if (empty($title) || empty($revision)) return '';

    $file = (!empty($all)) ? JSON_FILE_ALL : JSON_FILE;

    $file_text = read_file($file);

    if ($file_text == '') return '';

    $data = json_decode($file_text, true);

    if (!is_array($data)) return '';

    $data[$title] = $revision;

    file_write($file, json_encode($data));
    return $data;
}

/**
 * Get wikitext and revision ID from cached JSON data
 *
 * @param string $title The page title
 * @param string $all Whether to use the 'all' data file (non-empty string) or main file (empty string)
 * @return array{0: string, 1: string} Array containing [wikitext, revision_id]
 */
function get_from_json(string $title, string $all): array
{
    $revid = get_title_revision($title, $all);

    if (empty($revid) || !ctype_digit($revid)) {
        return ['', ''];
    }

    $file_dir = get_file_dir($revid, $all);

    if (!is_dir($file_dir)) return ['', ''];

    $wikitext = read_file($file_dir . "/wikitext.txt");

    return [$wikitext, $revid];
}
