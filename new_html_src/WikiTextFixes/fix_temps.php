<?php

namespace Fixes\FixTemps;

/*
Usage:

use function Fixes\FixTemps\add_missing_title;

*/

use function WikiParse\Template\getTemplates;

function add_missing_title($text, $title, $ljust = 17)
{
    // ---
    $temps = [
        "drugbox" => "drug_name",
        "infobox drug" => "drug_name",
        "infobox medical condition" => "name",
        "infobox medical intervention" => "name",

    ];
    // ---
    $temps_in = getTemplates($text);
    // ---
    $new_text = $text;
    // ---
    foreach ($temps_in as $temp) {
        // ---
        $name = strtolower($temp->getStripName());
        // ---
        if (!isset($temps[$name])) {
            continue;
        }
        // ---
        $old_text_template = $temp->getTemplateText();
        // ---
        $param = $temps[$name];
        // ---
        $name_p = $temp->getParameter($param, "");
        // ---
        if (!$name_p || empty(trim($name_p))) {
            $temp->setParameter($param, $title);
            // ---
            // $new_temp = str_replace('{{' . $temp_name, '{{' . $temp_name . "| $param = $title\n", $text_template);
            // ---
            $new_text_str = $temp->toString(true, $ljust);
            // ---
            $new_text = str_replace($old_text_template, $new_text_str, $new_text);
        }
    };
    // ---
    return $new_text;
}
