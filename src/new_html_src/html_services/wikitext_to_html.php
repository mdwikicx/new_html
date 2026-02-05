<?php

/**
 * HTML conversion services
 *
 * Provides functions for converting wikitext to HTML using the
 * Wikipedia REST API, with caching support.
 *
 * @package MDWiki\NewHtml
 */

namespace Html;
/*
use function Html\wiki_text_to_html;
*/

use function MDWiki\NewHtml\Infrastructure\Utils\fix_link_red;
use function MDWiki\NewHtml\Infrastructure\Utils\del_div_error;
use function MDWiki\NewHtml\FileHelps\file_write; // file_write($file_html, $result);
use function MDWiki\NewHtml\FileHelps\read_file;
use function MDWiki\NewHtml\Services\Api\convert_wikitext_to_html;

/**
 * Convert wikitext to HTML using the API and apply fixes
 *
 * @param string $wikitext The wikitext to convert
 * @param string $title The page title for context
 * @return mixed The HTML result or empty string on failure
 */
function do_wiki_text_to_html(string $wikitext, string $title): mixed
{

    $title = str_replace(" ", "_", $title);

    if ($wikitext == '') return "";

    $fixed = convert_wikitext_to_html($wikitext, $title);

    $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? '';

    if ($result == '') return "";

    $result = del_div_error($result);
    $result = fix_link_red($result);    return $result;
}

/**
 * Convert wikitext to HTML with caching support
 *
 * @param string $wikitext The wikitext to convert
 * @param string $file_html The path to the cached HTML file
 * @param string $title The page title for context
 * @param bool $new Whether to force regeneration (true) or use cache (false)
 * @return array{0: string, 1: bool} Array containing [html_content, from_cache]
 */
function wiki_text_to_html(string $wikitext, string $file_html, string $title, bool $new): array
{

    $from_cache = false;

    if (!$new) {

        $text = read_file($file_html);

        if ($text != '') return [$text, true];
    }

    if ($wikitext == '') return ["", $from_cache];

    $result = do_wiki_text_to_html($wikitext, $title);

    if ($result == '') return ["", $from_cache];

    file_write($file_html, $result);

    return [$result, $from_cache];
}
