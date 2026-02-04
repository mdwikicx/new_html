<?php

/**
 * HTML conversion services
 *
 * Backward compatibility wrapper for Services\Html\WikitextToHtmlService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Html\WikitextToHtmlService instead
 * @package MDWiki\NewHtml
 */

namespace Html;

use function MDWiki\NewHtml\Services\Html\do_wiki_text_to_html as NewDoWikiTextToHtml;
use function MDWiki\NewHtml\Services\Html\wiki_text_to_html as NewWikiTextToHtml;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Html\do_wiki_text_to_html instead
 */
function do_wiki_text_to_html(string $wikitext, string $title): mixed
{
    return NewDoWikiTextToHtml($wikitext, $title);
}

/**
 * @deprecated Use MDWiki\NewHtml\Services\Html\wiki_text_to_html instead
 */
function wiki_text_to_html(string $wikitext, string $file_html, string $title, bool $new): array
{
    return NewWikiTextToHtml($wikitext, $file_html, $title, $new);
}
