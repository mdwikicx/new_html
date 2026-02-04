<?php

/**
 * Wiki category parsing utilities - DEPRECATED
 *
 * This file provides backward compatibility wrappers.
 * New code should use MDWiki\NewHtml\Domain\Parser namespace instead.
 *
 * @package MDWiki\NewHtml\WikiParse
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\CategoryParser instead
 */

namespace WikiParse\Category;

use function MDWiki\NewHtml\Domain\Parser\get_categories as new_get_categories;

/*
Usage (DEPRECATED):

use function WikiParse\Category\get_categories;

New usage:
use function MDWiki\NewHtml\Domain\Parser\get_categories;

*/

/**
 * Extract all categories from wikitext
 *
 * @param string $text The wikitext to parse
 * @return array<string, string> Array mapping category names to their full [[Category:...]] tags
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_categories instead
 */
function get_categories(string $text): array
{
    return new_get_categories($text);
}
