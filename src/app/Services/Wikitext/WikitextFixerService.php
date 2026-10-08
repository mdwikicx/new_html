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

use MDWiki\NewHtml\Domain\Fixes\Media\FixImagesFixture;
use MDWiki\NewHtml\Domain\Fixes\Media\RemoveMissingImagesService;
use MDWiki\NewHtml\Domain\Fixes\References\DeleteEmptyRefsFixture;
use MDWiki\NewHtml\Domain\Fixes\References\ExpandRefsFixture;
use MDWiki\NewHtml\Domain\Fixes\References\RefWorkerFixture;
use MDWiki\NewHtml\Domain\Fixes\Structure\FixCategoriesFixture;
use MDWiki\NewHtml\Domain\Fixes\Templates\DeleteTemplatesFixture;
use MDWiki\NewHtml\Domain\Fixes\Templates\FixTemplatesFixture;
use MDWiki\NewHtml\Domain\Parser\LeadSectionParser;
use MDWiki\NewHtml\Services\Api\CommonsImageService;
use MDWiki\NewHtml\Services\Interfaces\CommonsImageServiceInterface;

class WikitextFixerService
{
    private CommonsImageServiceInterface $imageService;

    /**
     * Constructor
     *
     * @param CommonsImageServiceInterface $imageService Service for checking image existence
     */
    public function __construct(?CommonsImageServiceInterface $imageService = null)
    {
        $this->imageService = $imageService ?? new CommonsImageService();
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
        $text = DeleteTemplatesFixture::remove_templates($text);
        $text = DeleteTemplatesFixture::remove_lead_templates($text);

        // Clean up references
        $text = RefWorkerFixture::remove_bad_refs($text);
        $text = DeleteEmptyRefsFixture::del_empty_refs($text);

        // Remove language links
        // $text = FixLanguageLinksFixture::remove_lang_links($text);

        // Remove videos
        $text = FixImagesFixture::remove_videos($text);
        // $text = FixImagesFixture::remove_images($text);

        // Remove categories
        $text = FixCategoriesFixture::removeCategories($text);

        // Handle missing images and add title
        $service = new RemoveMissingImagesService($this->imageService);
        $text    = $service->run($text);

        // Add a missing title parameter to infobox templates.
        $text = FixTemplatesFixture::add_missing_title($text, $title);
        // *******************
        // *******************

        return $text;
    }

    /**
     * Extracts the lead section from the given text and expands its references if applicable.
     */
    public function stripTextIntoLeadSection(string $text): string
    {
        $lead = LeadSectionParser::get_lead_section($text);

        if ($lead && $lead !== $text) {
            return ExpandRefsFixture::expand_text_refs($lead, $text);
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
