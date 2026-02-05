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

use function MDWiki\NewHtml\Services\Api\get_wikitext_from_mdwiki_restapi;

/**
 * Get wikitext for a page, optionally processing only the lead section
 *
 * @param string $title The page title to fetch
 * @return array{0: string, 1: string|int} Array containing [wikitext, revision_id]
 */
function get_wikitext(string $title, string $file): array
{

    $title = str_replace(" ", "_", $title);

    $json1 = get_wikitext_from_mdwiki_restapi($title);

    $source = $json1[0];
    $revid = $json1[1];

    // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        test_print("Redirecting to: $title\n");
        $json1 = get_wikitext_from_mdwiki_restapi($title);
        $source = $json1[0];
        $revid = $json1[1];
    }

    if ($source != '') {

        test_print("source is not empty\n");
        test_print("get_lead_section: \n");
        $full_text = $source;
        $lead = get_lead_section($source);
        if ($lead != '') {
            $source = refs_expend_work($lead, $full_text);
        }

        $source = fix_wikitext($source, $title);
    }

    if (empty($source)) {
        test_print("wikitext empty!.");
    };

    if (!empty($revid)) {
        add_title_revision($title, $revid, $file);
    }

    return [$source, $revid];
}

/**
 * Get full wikitext for a page
 *
 * @param string $title The page title to fetch
 * @return array{0: string, 1: string|int} Array containing [wikitext, revision_id]
 */
function get_wikitext_all(string $title, string $file): array
{

    $title = str_replace(" ", "_", $title);

    $json1 = get_wikitext_from_mdwiki_restapi($title);

    $source = $json1[0];
    $revid = $json1[1];

    // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        test_print("Redirecting to: $title\n");
        $json1 = get_wikitext_from_mdwiki_restapi($title);
        $source = $json1[0];
        $revid = $json1[1];
    }

    if ($source != '') {

        test_print("source is not empty\n");

        $source = fix_wikitext($source, $title);
    }

    if (empty($source)) {
        test_print("wikitext empty!.");
    };

    if (!empty($revid)) {
        add_title_revision($title, $revid, $file);
    }

    return [$source, $revid];
}
