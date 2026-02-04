<?php

namespace MDWiki\NewHtml\Domain\Parser;

function get_lead_section_old(string $wikitext): string
{
    if (empty($wikitext) || strpos($wikitext, '==') === false) {
        return $wikitext;
    }

    $sections = preg_split('/==+/', $wikitext, 2, PREG_SPLIT_NO_EMPTY);
    $lead = $sections[0] ?? '';

    if (empty($lead)) {
        return $wikitext;
    }
    $lead .= "\n==References==\n<references />";

    return $lead;
}

function get_lead_section(string $wikitext): string
{
    if (empty($wikitext)) {
        return $wikitext;
    }

    if (strpos($wikitext, '==') === false) {
        return $wikitext;
    }

    $sections = preg_split('/^\s*==+/m', $wikitext, 2);
    $lead = $sections[0] ?? '';

    $lead = trim($lead);

    if (empty($lead)) {
        return "";
    }

    $lead .= "\n==References==\n<references />";

    return $lead;
}
