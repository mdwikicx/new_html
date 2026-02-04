<?php

/**
 * Wikitext fixing orchestration
 *
 * Backward compatibility wrapper for Services\Wikitext\WikitextFixerService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Wikitext\WikitextFixerService instead
 * @package MDWiki\NewHtml
 */

namespace FixText;

use function MDWiki\NewHtml\Services\Wikitext\fix_wikitext as NewFixWikitext;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Wikitext\fix_wikitext instead
 */
function fix_wikitext(string $text, string $title): string
{
    return NewFixWikitext($text, $title);
}
