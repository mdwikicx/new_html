<?php

namespace Lead;
/*
use function Lead\get_lead_section;
*/

function get_lead_section($wikitext)
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
