<?php

namespace MDWiki\NewHtml\Infrastructure\Storage;

use function MDWiki\NewHtml\Infrastructure\Utils\get_file_dir;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;

$json_file = dirname(dirname(__DIR__)) . '/revisions_new/json_data.json';
$json_file_all = dirname(dirname(__DIR__)) . '/revisions_new/json_data_all.json';

$json_dir = dirname($json_file);
if (!is_dir($json_dir)) {
    mkdir($json_dir, 0755, true);
}

if (!file_exists($json_file)) {
    file_write($json_file, '{}');
}

if (!file_exists($json_file_all)) {
    file_write($json_file_all, '{}');
}

function dump_both_data(array $main_data, array $main_data_all): void
{
    global $json_file_all, $json_file;

    file_write($json_file ?? '', json_encode($main_data, JSON_PRETTY_PRINT));
    file_write($json_file_all ?? '', json_encode($main_data_all, JSON_PRETTY_PRINT));
}

function get_Data(string $tyt): array
{
    global $json_file_all, $json_file;

    $file = ($tyt == 'all') ? ($json_file_all ?? '') : ($json_file ?? '');

    $file_text = read_file($file);

    if ($file_text == '') return [];

    $data = json_decode($file_text, true) ?? [];
    return $data;
}

function get_title_revision(string $title, string $all): string
{
    global $json_file_all, $json_file;

    $file = (!empty($all)) ? ($json_file_all ?? '') : ($json_file ?? '');

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
    global $json_file_all, $json_file;

    if (empty($title) || empty($revision)) return '';

    $file = (!empty($all)) ? ($json_file_all ?? '') : ($json_file ?? '');

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
