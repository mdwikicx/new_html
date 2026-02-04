<?php
/**
 * Template fixing utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\Templates\FixTemplatesFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Templates namespace instead
 */

namespace Fixes\FixTemps;

use function MDWiki\NewHtml\Domain\Fixes\Templates\add_missing_title as new_add_missing_title;

/*
Usage:

use function Fixes\FixTemps\add_missing_title;

*/

/**
 * Add missing title parameter to infobox templates
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Templates\add_missing_title instead
 * @param string $text The wikitext to process
 * @param string $title The page title to add
 * @param int $ljust Left justify parameter names to this width (default 17)
 * @return string The wikitext with updated templates
 */
function add_missing_title(string $text, string $title, int $ljust = 17): string
{
    return new_add_missing_title($text, $title, $ljust);
}
