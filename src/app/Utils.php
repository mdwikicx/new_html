<?php
namespace MDWiki\NewHtml\Utils;

/**
 * Get the file directory for a specific revision
 *
 * @param string $revision The revision ID
 * @param string $all Whether to use the '_all' suffix (non-empty string) or not (empty string)
 * @return string The directory path, or empty string on error
 */

use MDWiki\NewHtml\Logger;

function get_file_dir(string $revision, string $all): string
{
    if (empty($revision) || ! ctype_digit($revision)) {
        Logger::error('Error: revision is empty in get_file_dir().');
        return '';
    }

    $file_dir = REVISIONS_PATH . "/$revision";

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

