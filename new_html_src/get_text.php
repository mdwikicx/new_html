<?php

namespace Wikitext;
/*
use function Wikitext\get_wikitext;

*/

use function FixText\fix_wikitext;
use function Lead\get_lead_section;
use function NewHtml\JsonData\add_title_revision;
use function Printn\test_print;
use function Fixes\ExpendRefs\refs_expend_work;

use function APIServices\get_wikitext_from_mdwiki_restapi;

function get_wikitext($title, $all)
{
    // ---
    $title = str_replace(" ", "_", $title);
    // ---
    $json1 = get_wikitext_from_mdwiki_restapi($title);
    // ---
    $source = $json1[0];
    $revid = $json1[1];
    // ---
    // if $source match #REDIRECT [[.*?]] then get the wikitext from target page
    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        test_print("Redirecting to: $title\n");
        $json1 = get_wikitext_from_mdwiki_restapi($title);
        $source = $json1[0];
        $revid = $json1[1];
    }
    // ---
    if ($source != '') {
        // ---
        test_print("source is not empty\n");
        // ---
        if ($all == '') {
            test_print("get_lead_section: \n");
            $full_text = $source;
            $lead = get_lead_section($source);
            if ($lead != '') {
                $source = refs_expend_work($lead, $full_text);
            }
        }
        // ---
        $source = fix_wikitext($source, $title);
    }
    // ---
    if ($source == "") {
        test_print("wikitext empty!.");
    };
    // ---
    if (!empty($revid)) {
        add_title_revision($title, $revid, $all);
    }
    // ---
    return [$source, $revid];
}
