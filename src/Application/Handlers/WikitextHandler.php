<?php

/**
 * Wikitext fetching and processing
 *
 * Provides functions for fetching wikitext from MDWiki, processing
 * redirect pages, and applying fixes to the content.
 *
 * @package MDWiki\NewHtml
 */

namespace MDWiki\NewHtml\Application\Handlers;

use function MDWiki\NewHtml\Services\Wikitext\fix_wikitext;
use function MDWiki\NewHtml\Domain\Parser\get_lead_section;
use function MDWiki\NewHtml\Application\Controllers\add_title_revision;
use function MDWiki\NewHtml\Infrastructure\Debug\test_print;
use function MDWiki\NewHtml\Domain\Fixes\References\refs_expend_work;
use function MDWiki\NewHtml\Services\Api\getWikitextFromMdwikiRestApi;

/**
 * Get wikitext for a page
 *
 * @param string $title The page title to fetch
 * @param string $file The file to save the title and revision to
 * @param bool $just_lead Whether to process only the lead section
 * @return array{source: string, revid: string|int, error: string}
 */
function get_wikitext(string $title, string $file, bool $just_lead = false): array
{

    $title = str_replace(" ", "_", $title);
    $json1 = getWikitextFromMdwikiRestApi($title);

    // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $json1["source"], $matches)) {
        $title = $matches[1];
        test_print("Redirecting to: $title\n");
        $json1 = getWikitextFromMdwikiRestApi($title);
    }

    $source = $json1["source"];
    $revid  = $json1["revid"];
    $error  = $json1["error"];

    $result = [
        "source" => $source,
        "revid" => $revid,
        "error" => $error,
    ];

    if (!empty($revid)) {
        add_title_revision($title, $revid, $file);
    }

    if (empty($source)) {
        error_log("WikitextHandler: wikitext empty for title: $title");
        test_print("wikitext empty!.");
        return $result;
    };

    test_print("source is not empty\n");

    if ($just_lead) {
        test_print("get_lead_section: \n");
        $full_text = $source;
        $lead = get_lead_section($source);
        if (!empty($lead)) {
            $source = refs_expend_work($lead, $full_text);
        }
    }

    $source = fix_wikitext($source, $title);
    $result["source"] = $source;

    return $result;
}
