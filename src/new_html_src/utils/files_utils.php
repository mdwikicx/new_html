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

// Backward compatibility - delegate to new namespace
use function MDWiki\NewHtml\Infrastructure\Utils\get_revisions_new_dir as new_get_revisions_new_dir;
use function MDWiki\NewHtml\Infrastructure\Utils\get_file_dir as new_get_file_dir;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write as new_file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file as new_read_file;

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\get_revisions_new_dir instead
 */
function get_revisions_new_dir(): string
{
    return new_get_revisions_new_dir();
}

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\get_file_dir instead
 */
function get_file_dir(string $revision, string $all): string
{
    return new_get_file_dir($revision, $all);
}

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\file_write instead
 */
function file_write(?string $file, string $text): void
{
    new_file_write($file, $text);
}

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\read_file instead
 */
function read_file(?string $file): bool|string
{
    return new_read_file($file);
}
