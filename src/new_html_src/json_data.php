<?php

namespace NewHtml\JsonData;

if (defined('DEBUGX') && DEBUGX) {
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

$json_file = __DIR__ . "/../../../revisions_new/json_data.json";
$json_file_all = __DIR__ . "/../../../revisions_new/json_data_all.json";

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

function dump_both_data($main_data, $main_data_all)
{
    global $json_file_all, $json_file;
    // ---
    file_write($json_file, json_encode($main_data, JSON_PRETTY_PRINT));
    file_write($json_file_all, json_encode($main_data_all, JSON_PRETTY_PRINT));
}
function get_Data($tyt)
{
    global $json_file_all, $json_file;
    // ---
    $file = ($tyt == 'all') ? $json_file_all : $json_file;
    // ---
    $file_text = read_file($file);
    // ---
    if ($file_text == '') return [];
    // ---
    $data = json_decode($file_text, true) ?? [];
    // ---
    return $data;
}

function get_title_revision($title, $all)
{
    global $json_file_all, $json_file;
    // ---
    $file = (!empty($all)) ? $json_file_all : $json_file;
    // ---
    $file_text = read_file($file);
    // ---
    if ($file_text == '') return '';
    // ---
    $data = json_decode($file_text, true);
    // ---
    if (!is_array($data)) return '';
    // ---
    if (array_key_exists($title, $data)) {
        return $data[$title];
    }
    // ---
    return "";
}

function add_title_revision($title, $revision, $all)
{
    global $json_file_all, $json_file;
    // ---
    if (empty($title) || empty($revision)) return '';
    // ---
    $file = (!empty($all)) ? $json_file_all : $json_file;
    // ---
    $file_text = read_file($file);
    // ---
    if ($file_text == '') return '';
    // ---
    $data = json_decode($file_text, true);
    // ---
    if (!is_array($data)) return '';
    // ---
    $data[$title] = $revision;
    // ---
    file_write($file, json_encode($data));
    // ---
    return $data;
}

function get_from_json($title, $all)
{
    $revid = get_title_revision($title, $all);
    // ---
    if (empty($revid) || !ctype_digit($revid)) {
        return ['', ''];
    }
    // ---
    $file_dir = get_file_dir($revid, $all);
    // ---
    if (!is_dir($file_dir)) return ['', ''];
    // ---
    $wikitext = read_file($file_dir . "/wikitext.txt");
    // ---
    return [$wikitext, $revid];
}
