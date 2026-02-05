<?php

/**
 * Wikitext fixing orchestration
 *
 * Provides the main entry point for applying various wikitext fixes,
 * coordinating multiple fix functions in a pipeline.
 *
 * @package MDWiki\NewHtml\Services\Wikitext
 */

namespace MDWiki\NewHtml\Services\Wikitext;

use function MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs;
use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_categories;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_videos;
use function MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_lead_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\add_missing_title;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_images;
// use function MDWiki\NewHtml\Domain\Fixes\Media\remove_images;
// use function Fixes\fix_langs_links\remove_lang_links;

/**
 * Fix wikitext by removing unwanted templates, refs, and other elements
 *
 * @param string $text The wikitext to fix
 * @param string $title The page title for context
 * @return string The fixed wikitext
 */
function fix_wikitext(string $text, string $title): string
{
    $text = str_replace("{{drugbox", "{{Infobox drug", $text);
    $text = str_replace("{{Drugbox", "{{Infobox drug", $text);

    $text = remove_templates($text);
    $text = remove_lead_templates($text);

    $text = remove_bad_refs($text);
    $text = del_empty_refs($text);

    // $text = remove_lang_links($text);

    $text = remove_videos($text);

    // $text = remove_images($text);

    $text = remove_categories($text);

    $text = remove_missing_images($text);

    $text = add_missing_title($text, $title);

    return $text;
}
