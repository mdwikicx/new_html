<?php
/**
 * Category removal utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\Structure\FixCategoriesFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Structure namespace instead
 */

namespace Fixes\FixCats;

use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_categories as new_remove_categories;

/*
Usage:

use function Fixes\FixCats\remove_categories;

*/

/**
 * Remove all category tags from wikitext
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Structure\remove_categories instead
 * @param string $text The wikitext to process
 * @return string The wikitext with categories removed
 */
function remove_categories(string $text): string
{
    return new_remove_categories($text);
}
