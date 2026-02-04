<?php

namespace Lead;
/*
use function Lead\get_lead_section;
*/

function get_lead_section($wikitext)
{
    if (empty($wikitext) || strpos($wikitext, '==') === false) {
        return $wikitext;
    }

    // Split the wikitext into sections by lines starting with ==+ and get only the first section
    $sections = preg_split('/==+/', $wikitext, 2, PREG_SPLIT_NO_EMPTY);
    $lead = $sections[0] ?? '';

    if (empty($lead)) {
        return $wikitext;
    }

    $lead .= "\n==References==\n<references />";

    return $lead;
}
