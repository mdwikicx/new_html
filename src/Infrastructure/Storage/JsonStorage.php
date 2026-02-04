<?php

namespace MDWiki\NewHtml\Infrastructure\Storage;

use function MDWiki\NewHtml\Infrastructure\Utils\get_file_dir;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;

$GLOBALS['MDWIKI_JSON_FILE'] = dirname(dirname(__DIR__)) . '/revisions_new/json_data.json';
$GLOBALS['MDWIKI_JSON_FILE_ALL'] = dirname(dirname(__DIR__)) . '/revisions_new/json_data_all.json';

$json_dir = dirname($GLOBALS['MDWIKI_JSON_FILE']);
if (!is_dir($json_dir)) {
    mkdir($json_dir, 0755, true);
}

if (!file_exists($GLOBALS['MDWIKI_JSON_FILE'])) {
    file_write($GLOBALS['MDWIKI_JSON_FILE'], '{}');
}

if (!file_exists($GLOBALS['MDWIKI_JSON_FILE_ALL'])) {
    file_write($GLOBALS['MDWIKI_JSON_FILE_ALL'], '{}');
}

function dump_both_data(array $main_data, array $main_data_all): void
{
    file_write($GLOBALS['MDWIKI_JSON_FILE'] ?? '', json_encode($main_data, JSON_PRETTY_PRINT));
    file_write($GLOBALS['MDWIKI_JSON_FILE_ALL'] ?? '', json_encode($main_data_all, JSON_PRETTY_PRINT));
}

function get_Data(string $tyt): array
{
    $file = ($tyt == 'all') ? ($GLOBALS['MDWIKI_JSON_FILE_ALL'] ?? '') : ($GLOBALS['MDWIKI_JSON_FILE'] ?? '');

    $file_text = read_file($file);

    if ($file_text == '') return [];

    $data = json_decode($file_text, true) ?? [];
    return $data;
}

function get_title_revision(string $title, string $all): string
{
    $file = (!empty($all)) ? ($GLOBALS['MDWIKI_JSON_FILE_ALL'] ?? '') : ($GLOBALS['MDWIKI_JSON_FILE'] ?? '');

    $file_text = read_file($file);

    if ($file_text == '') return '';

    $data = json_decode($file_text, true);

    if (!is_array($data)) return '';

    if (array_key_exists($title, $data)) {
        return $data[$title];
    }
    return "";
}

function add_title_revision(string $title, string $revision, string $all): array|string
{
    if (empty($title) || empty($revision)) return '';

    $file = (!empty($all)) ? ($GLOBALS['MDWIKI_JSON_FILE_ALL'] ?? '') : ($GLOBALS['MDWIKI_JSON_FILE'] ?? '');

    $file_text = read_file($file);

    if ($file_text == '') return '';

    $data = json_decode($file_text, true);

    if (!is_array($data)) return '';

    $data[$title] = $revision;

    file_write($file, json_encode($data));
    return $data;
}

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
