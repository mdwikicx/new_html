<?php

/**
 * Lead section extraction utilities
 *
 * Provides functions for extracting the lead section from
 * MediaWiki wikitext, which is the content before the first
 * section heading.
 *
 * @package MDWiki\NewHtml\Lead
 */

namespace MDWiki\NewHtml\Domain\Parser;
/*
use function MDWiki\NewHtml\Domain\Parser\get_lead_section;
*/

/**
 * Get the lead section of wikitext
 *
 * @param string $wikitext The wikitext to process
 * @return string The lead section with references tag appended, or empty string if no lead
 */
function get_lead_section(string $wikitext): string
{
    if (empty($wikitext)) {
        return $wikitext;
    }

    // Check if there's no heading (strpos returns false)
    if (strpos($wikitext, '==') === false) {
        return $wikitext;
    }

    // Split by lines that start with optional whitespace then == (heading markers)
    // Use multiline mode with ^ to match start of line
    $sections = preg_split('/^\s*==+/m', $wikitext, 2);
    $lead = $sections[0] ?? '';

    // Trim the lead section
    $lead = trim($lead);

    // If lead is empty or only whitespace, return empty string
    if (empty($lead)) {
        return "";
    }

    $lead .= "\n==References==\n<references />";

    return $lead;
}
