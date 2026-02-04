<?php

namespace FixText;

/*
Usage:

use function FixText\fix_wikitext;

*/


use function Fixes\DelMtRefs\del_empty_refs;
use function Fixes\FixCats\remove_categories;
use function Fixes\FixImages\remove_videos;
// use function Fixes\FixImages\remove_images;
// use function Fixes\fix_langs_links\remove_lang_links;
use function Fixes\RefWork\remove_bad_refs;
use function Fixes\DelTemps\remove_templates;
use function Fixes\DelTemps\remove_lead_templates;
use function Fixes\FixTemps\add_missing_title;

function fix_wikitext($text, $title)
{
    $text = str_replace("{{drugbox", "{{Infobox drug", $text);
    $text = str_replace("{{Drugbox", "{{Infobox drug", $text);
    // ---
    $text = remove_templates($text);
    $text = remove_lead_templates($text);
    $text = add_missing_title($text, $title);
    // ---
    $text = remove_bad_refs($text);
    $text = del_empty_refs($text);
    // ---
    // $text = remove_lang_links($text);
    // ---
    $text = remove_videos($text);
    // ---
    // $text = remove_images($text);
    // ---
    $text = remove_categories($text);
    // ---
    return $text;
}
