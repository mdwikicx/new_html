<?php

namespace NewHtml\FileHelps;

if (defined('DEBUGX') && DEBUGX) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

use function Printn\test_print;

$revisions_new_dir = dirname(dirname(__DIR__)) . '/revisions_new';

function get_revisions_new_dir()
{
    global $revisions_new_dir;
    return $revisions_new_dir;
}

function get_file_dir($revision, $all)
{
    global $revisions_new_dir;
    // ---
    if (empty($revision) || !ctype_digit($revision)) {
        test_print('Error: revision is empty in get_file_dir().');
        return '';
    }
    // ---
    $file_dir = $revisions_new_dir . "/$revision";
    // ---
    if ($all != '') $file_dir .= "_all";
    // ---
    if (!is_dir($file_dir)) {
        if (!mkdir($file_dir, 0755, true)) {
            test_print(sprintf('Failed to create directory "%s".', $file_dir));
        }
    }
    // ---
    return $file_dir;
}

function file_write($file, $text)
{
    if (empty($text) || empty($file)) {
        return;
    }
    // ---
    try {
        file_put_contents($file, $text, LOCK_EX);
    } catch (\Exception $e) {
        test_print("Error: Could not write to file: $file");
    }
}

function read_file($file)
{
    // ---
    if (empty($file) || !file_exists($file)) {
        return "";
    }
    // ---
    try {
        return file_get_contents($file);
    } catch (\Exception $e) {
        test_print("Error: Could not read file: $file");
    }
    // ---
    return "";
}
