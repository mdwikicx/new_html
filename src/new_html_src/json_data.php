<?php

/**
 * JSON data storage for title-revision mappings (Backward Compatibility)
 *
 * @deprecated Use MDWiki\NewHtml\Application\Controllers\JsonDataController instead
 * @package MDWiki\NewHtml
 */

namespace NewHtml\JsonData;

use function MDWiki\NewHtml\Application\Controllers\dump_both_data as new_dump_both_data;
use function MDWiki\NewHtml\Application\Controllers\get_Data as new_get_Data;
use function MDWiki\NewHtml\Application\Controllers\get_title_revision as new_get_title_revision;
use function MDWiki\NewHtml\Application\Controllers\add_title_revision as new_add_title_revision;
use function MDWiki\NewHtml\Application\Controllers\get_from_json as new_get_from_json;

/**
 * @deprecated Use MDWiki\NewHtml\Application\Controllers\dump_both_data
 * @param array<string, mixed> $main_data
 * @param array<string, mixed> $main_data_all
 */
function dump_both_data(array $main_data, array $main_data_all): void
{
    new_dump_both_data($main_data, $main_data_all);
}

/**
 * @deprecated Use MDWiki\NewHtml\Application\Controllers\get_Data
 * @return array<string, mixed>
 */
function get_Data(string $tyt): array
{
    return new_get_Data($tyt);
}

/**
 * @deprecated Use MDWiki\NewHtml\Application\Controllers\get_title_revision
 */
function get_title_revision(string $title, string $all): string
{
    return new_get_title_revision($title, $all);
}

/**
 * @deprecated Use MDWiki\NewHtml\Application\Controllers\add_title_revision
 * @return array<string, mixed>|string
 */
function add_title_revision(string $title, string $revision, string $all): array|string
{
    return new_add_title_revision($title, $revision, $all);
}

/**
 * @deprecated Use MDWiki\NewHtml\Application\Controllers\get_from_json
 * @return array{0: string, 1: string}
 */
function get_from_json(string $title, string $all): array
{
    return new_get_from_json($title, $all);
}
