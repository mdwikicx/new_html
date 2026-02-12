<?php

/**
 * Language link removal utilities
 *
 * Provides functions for removing interwiki language links from wikitext.
 *
 * @package MDWiki\NewHtml\Domain\Fixes\Structure
 */

namespace MDWiki\NewHtml\Domain\Fixes\Structure;

/**
 * Remove language links from wikitext
 *
 * This function uses a regex pattern to match interwiki language links
 * like [[en:Article]] or [[be-tarask:Артыкул]] and removes them.
 *
 * The pattern matches language codes that:
 * - Start with at least 2 lowercase letters
 * - Can have additional parts separated by hyphens (e.g., be-tarask, zh-min-nan)
 *
 * @param string $text The wikitext to process
 * @return string The wikitext with language links removed
 */
function remove_lang_links(string $text): string
{
    // Pattern matches: [[language_code:article_name]]
    // Language codes: 2+ lowercase letters, optionally followed by hyphen-letter groups
    $pattern = '/\[\[([a-z]{2,}(?:-[a-z]+)*):[^\]]+\]\]/';

    preg_match_all($pattern, $text, $matches);

    foreach ($matches[0] as $link) {
        $text = str_replace($link, '', $text);
    }

    return $text;
}

/**
 * Check if a given code matches Wikipedia language code pattern
 *
 * @param string $code The code to validate
 * @return bool True if the code matches the language code pattern
 */
function is_valid_lang_code(string $code): bool
{
    // Pattern: 2+ lowercase letters, optionally followed by hyphen-letter groups
    return (bool) preg_match('/^[a-z]{2,}(?:-[a-z]+)*$/', $code);
}
