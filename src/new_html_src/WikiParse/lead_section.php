<?php

/**
 * Lead section extraction utilities - DEPRECATED
 *
 * This file provides backward compatibility wrappers.
 * New code should use MDWiki\NewHtml\Domain\Parser namespace instead.
 *
 * @package MDWiki\NewHtml\Lead
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\LeadSectionParser instead
 */

namespace Lead;

use function MDWiki\NewHtml\Domain\Parser\get_lead_section_old as new_get_lead_section_old;
use function MDWiki\NewHtml\Domain\Parser\get_lead_section as new_get_lead_section;

/*
Usage (DEPRECATED):
use function Lead\get_lead_section;

New usage:
use function MDWiki\NewHtml\Domain\Parser\get_lead_section;
*/

/**
 * Get the lead section of wikitext (old implementation)
 *
 * @param string $wikitext The wikitext to process
 * @return string The lead section with references tag appended
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_lead_section_old instead
 */
function get_lead_section_old(string $wikitext): string
{
    return new_get_lead_section_old($wikitext);
}

/**
 * Get the lead section of wikitext
 *
 * @param string $wikitext The wikitext to process
 * @return string The lead section with references tag appended, or empty string if no lead
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_lead_section instead
 */
function get_lead_section(string $wikitext): string
{
    return new_get_lead_section($wikitext);
}
