<?php

/**
 * Language link removal utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\Structure\FixLanguageLinksFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Structure namespace instead
 */

namespace Fixes\fix_langs_links;

use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_lang_links as new_remove_lang_links;

/*
Usage:

use function Fixes\fix_langs_links\remove_lang_links;

*/

/**
 * Remove language links from wikitext
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Structure\remove_lang_links instead
 * @param string $text The wikitext to process
 * @return string The wikitext with language links removed
 */
function remove_lang_links(string $text): string
{
    return new_remove_lang_links($text);
}
