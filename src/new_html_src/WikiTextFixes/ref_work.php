<?php
/**
 * Reference quality checking utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\References\RefWorkerFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References namespace instead
 */

namespace Fixes\RefWork;

use function MDWiki\NewHtml\Domain\Fixes\References\check_one_cite as new_check_one_cite;
use function MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs as new_remove_bad_refs;

/*
Usage:

use function Fixes\RefWork\check_one_cite;
use function Fixes\RefWork\remove_bad_refs;

*/

/**
 * Check if the citation contains self-published information and filter it out.
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References\check_one_cite instead
 * @param string $cite The citation text to check
 * @return bool Returns true if self-published information is found and removed, false otherwise
 */
function check_one_cite(string $cite): bool
{
    return new_check_one_cite($cite);
}

/**
 * Removes bad references from the provided text based on citation tags.
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs instead
 * @param string $text The text containing references to check and potentially remove
 * @return string The text with bad references removed
 */
function remove_bad_refs(string $text): string
{
    return new_remove_bad_refs($text);
}
