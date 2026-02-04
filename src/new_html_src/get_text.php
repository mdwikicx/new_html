<?php

/**
 * Wikitext fetching and processing (Backward Compatibility)
 *
 * @deprecated Use MDWiki\NewHtml\Application\Handlers\WikitextHandler instead
 * @package MDWiki\NewHtml
 */

namespace Wikitext;

use function MDWiki\NewHtml\Application\Handlers\get_wikitext as new_get_wikitext;

/**
 * @deprecated Use MDWiki\NewHtml\Application\Handlers\get_wikitext
 * @return array{0: string, 1: string|int}
 */
function get_wikitext(string $title, string $all): array
{
    return new_get_wikitext($title, $all);
}
