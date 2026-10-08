<?php
namespace MDWiki\NewHtml\Infrastructure\Utils;

/**
 * File I/O helper utilities
 *
 * Provides functions for file and directory operations, including
 * reading, writing, and managing the revisions storage directory.
 *
 * @package MDWiki\NewHtml\Infrastructure\Utils
 */

use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Settings;

class FileUtils
{
    /**
     * Get the file directory for a specific revision
     *
     * @param string $revision The revision ID
     * @param string $all Whether to use the '_all' suffix (non-empty string) or not (empty string)
     * @return string The directory path, or empty string on error
     */

    public static function get_file_dir(string $revision, string $all): string
    {
        if (empty($revision) || ! ctype_digit($revision)) {
            Logger::error('Error: revision is empty in get_file_dir().');
            return '';
        }

        $settings = Settings::getInstance();
        $file_dir = $settings->RevisionsDirPath . "/$revision";

        if (! empty($all)) {
            $file_dir .= "_all";
        }

        if (! is_dir($file_dir)) {
            if (! mkdir($file_dir, 0755, true)) {
                Logger::error(sprintf('Failed to create directory "%s".', $file_dir));
            }
        }
        return $file_dir;
    }

    /**
     * Write text to a file with locking
     *
     * @param string|null $file The file path to write to
     * @param string $text The content to write
     * @return bool
     */
    public static function FileWrite(?string $file, string $text): bool
    {
        if (empty($text) || empty($file)) {
            return false;
        }

        try {
            file_put_contents($file, $text, LOCK_EX);
            return true;
        } catch (\Exception $e) {
            Logger::error("FileUtils: Could not write to file: $file - " . $e->getMessage());
            Logger::debug("Error: Could not write to file: $file");
            return false;
        }
    }

    /**
     * Read the contents of a file
     *
     * @param string|null $file The file path to read from
     * @return bool|string The file contents, or empty string on error
     */
    public static function readFile(?string $file): bool | string
    {

        if (empty($file) || ! file_exists($file)) {
            return "";
        }

        try {
            return file_get_contents($file);
        } catch (\Exception $e) {
            Logger::error("FileUtils: Could not read file: $file - " . $e->getMessage());
            Logger::debug("Error: Could not read file: $file");
        }

        return "";
    }

}
