<?php

/**
 * Empty reference handling utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\References\DeleteEmptyRefsFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References namespace instead
 */

namespace Fixes\DelMtRefs;

use function MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs as new_del_empty_refs;

/*
Usage:

use function Fixes\DelMtRefs\del_empty_refs;

*/

/**
 * Delete empty short refs or expand them with full ref definitions
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs instead
 * @param string $first The text containing short refs
 * @return string The text with empty refs removed and expandable refs replaced
 */
function del_empty_refs(string $first): string
{
    return new_del_empty_refs($first);
}
