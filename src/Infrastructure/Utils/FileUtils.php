<?php

namespace MDWiki\NewHtml\Infrastructure\Utils;

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

$GLOBALS['MDWIKI_NEW_HTML_REVISIONS_DIR'] = dirname(dirname(__DIR__)) . '/revisions_new';

function get_revisions_new_dir(): string
{
    return $GLOBALS['MDWIKI_NEW_HTML_REVISIONS_DIR'];
}

function get_file_dir(string $revision, string $all): string
{
    $revisions_new_dir = $GLOBALS['MDWIKI_NEW_HTML_REVISIONS_DIR'];

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
