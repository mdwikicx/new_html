<?php

/**
 * Wikitext fetching and processing
 *
 * Provides functions for fetching wikitext from MDWiki, processing
 * redirect pages, and applying fixes to the content.
 *
 * @package MDWiki\NewHtml
 */

namespace MDWiki\NewHtml\Handlers;

use MDWiki\NewHtml\Controllers\JsonDataController;
use MDWiki\NewHtml\Domain\Fixes\References\ExpandRefsFixture;
use MDWiki\NewHtml\Domain\Parser\LeadSectionParser;
use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Services\Api\MdwikiApiService;
use MDWiki\NewHtml\Services\Wikitext\WikitextFixerService;

class WikitextHandler
{
/**
 * Get wikitext for a page
 *
 * @param string $title The page title to fetch
 * @param string $file The file to save the title and revision to
 * @param bool $just_lead Whether to process only the lead section
 * @return array{source: string, revid: string|int, error: string}
 */
    public static function getWikitext(
        string $title,
        string $file,
        bool $just_lead = false
    ): array {

        $service = new MdwikiApiService();
        $title   = str_replace(" ", "_", $title);
        $json1   = $service->getWikitextFromMdwikiRestApi($title);

        // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
        if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $json1["source"], $matches)) {
            $title = $matches[1];
            Logger::debug("Redirecting to: $title\n");
            $json1 = $service->getWikitextFromMdwikiRestApi($title);
        }

        $source = $json1["source"];
        $revid  = $json1["revid"];
        $error  = $json1["error"];

        $result = [
            "source" => $source,
            "revid"  => $revid,
            "error"  => $error,
        ];

        if (! empty($revid)) {
            JsonDataController::addTitleRevision($title, $revid, $file);
        }

        if (empty($source)) {
            Logger::error("WikitextHandler: wikitext empty for title: $title");
            Logger::debug("wikitext empty!.");
            return $result;
        }

        Logger::debug("source is not empty\n");

        if ($just_lead) {
            Logger::debug("LeadSectionParser::get_lead_section: \n");
            $full_text = $source;
            $lead      = LeadSectionParser::get_lead_section($full_text);
            if (! empty($lead)) {
                $source = ExpandRefsFixture::expand_text_refs($lead, $full_text);
            }
        }
        $service = new WikitextFixerService();
        $source  = $service->fix($source, $title);

        $result["source"] = $source;

        return $result;
    }
}
