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

use MDWiki\NewHtml\Services\Api\CommonsImageService;
use MDWiki\NewHtml\Domain\Fixes\Media\RemoveMissingImagesService;

use function MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs;
use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_categories;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_videos;
use function MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_lead_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\add_missing_title;
use function MDWiki\NewHtml\Domain\Parser\getTemplates;
use function MDWiki\NewHtml\Domain\Parser\get_lead_section;
use function MDWiki\NewHtml\Domain\Fixes\References\expand_text_refs;
// use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_lang_links;

class WikitextFixerService
{
    public function __construct()
    {
        // init
    }

    /**
     * Fix wikitext by removing unwanted templates, refs, and other elements
     *
     * @param string $text The wikitext to fix
     * @param string $title The page title for context
     * @return string The fixed wikitext
     */
    public function fix(string $text, string $title): string
    {
        // Replace templates
        $text = str_replace("{{drugbox", "{{Infobox drug", $text);
        $text = str_replace("{{Drugbox", "{{Infobox drug", $text);

        // Clean up templates
        $text = remove_templates($text);
        $text = remove_lead_templates($text);

        // Clean up references
        $text = remove_bad_refs($text);
        $text = del_empty_refs($text);

        // Remove language links
        // $text = remove_lang_links($text);

        // Remove videos
        $text = remove_videos($text);
        // $text = remove_images($text);

        // Remove categories
        $text = remove_categories($text);

        // Handle missing images and add title
        $service = new RemoveMissingImagesService(new CommonsImageService());
        $text = $service->run($text);

        // Add a missing title parameter to infobox templates.
        $text = add_missing_title($text, $title);
        // *******************
        // *******************

        return $text;
    }

    /**
     * Extracts the lead section from the given text and expands its references if applicable.
     */
    public function stripTextIntoLeadSection(string $text): string
    {
        $lead = get_lead_section($text);

        if ($lead && $lead !== $text) {
            return expand_text_refs($lead, $text);
        }

        return $text;
    }

    public function run(string $text, string $title, bool $lead_only = false): string
    {
        if ($lead_only) {
            $text = $this->stripTextIntoLeadSection($text);
        }
        $text = $this->fix($text, $title);

        return $text;
    }
}

function expend_all_templates(string $text, int $ljust = 17): string
{
    $temps_in = getTemplates($text);
    $new_text = $text;

    foreach ($temps_in as $temp) {
        $old_text_template = $temp->getTemplateText();
        $new_text_str = $temp->toString(true, $ljust);
        $new_text = str_replace($old_text_template, $new_text_str, $new_text);
    };

    return $new_text;
}
