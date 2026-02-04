<?php

/**
 * Reference expansion utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\References\ExpandRefsFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References namespace instead
 */

namespace Fixes\ExpendRefs;

use function MDWiki\NewHtml\Domain\Fixes\References\refs_expend_work as new_refs_expend_work;

/*
Usage:

use function Fixes\ExpendRefs\refs_expend_work;

*/

/**
 * Expand short references by finding their full definitions in the text
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References\refs_expend_work instead
 * @param string $first The lead section text with short refs
 * @param string $alltext The full page text containing full ref definitions
 * @return string The text with short refs expanded to full refs
 */
function refs_expend_work(string $first, string $alltext): string
{
    return new_refs_expend_work($first, $alltext);
}
