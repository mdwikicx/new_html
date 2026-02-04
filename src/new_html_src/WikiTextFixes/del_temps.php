<?php

namespace Fixes\DelTemps;

/*
Usage:

use function Fixes\DelTemps\remove_templates;
use function Fixes\DelTemps\remove_lead_templates;

*/

use function WikiParse\Template\getTemplates;

function check_temps_patterns($name, $old_text_template, $new_text): string
{
    $temps_patterns = [
        // any template startswith pp-
        "/^pp(-.*)?$/",
        "/^articles (for|with|needing|containing).*$/",
        "/^engvar[ab]$/",
        "/^use[\sa-z]+(english|spelling|referencing)$/",
        "/^use [dmy]+ dates$/",
        "/^wikipedia articles (for|with|needing|containing).*$/",
        "/^(.*-)?stub$/"
    ];
    // ---
    foreach ($temps_patterns as $pattern) {
        if (preg_match($pattern, $name)) {
            $new_text = str_replace($old_text_template, '', $new_text);
            break;
        }
    }
    return $new_text;
}
function check_temp_to_delete($name): bool
{
    $tempsToDelete = [
        "rtt",
        "#unlinkedwikibase",
        "about",
        "anchor",
        "defaultsort",
        "distinguish",
        "esborrany",
        "featured article",
        "fr",
        "good article",
        "italic title",
        "other uses",
        "redirect",
        "redirect-distinguish",
        "see also",
        "short description",
        "sprotect",
        "tedirect-distinguish",
        "toc limit",
        "use american english",
        "use dmy dates",
        "use mdy dates",
        "void",
    ];
    // if $name start with "defaultsort" delete it
    if (strpos($name, "defaultsort") === 0) {
        return true;
    }
    // ---
    return in_array($name, $tempsToDelete);
}

function remove_templates($text): string
{
    $temps_in = getTemplates($text);
    // ---
    $new_text = $text;
    // ---
    foreach ($temps_in as $temp) {
        // ---
        $name = strtolower($temp->getStripName());
        // ---
        $old_text_template = $temp->getTemplateText();
        // ---
        if (check_temp_to_delete($name)) {
            $new_text = str_replace($old_text_template, '', $new_text);
            continue;
        }
        // ---        // if $name start with "#unlinkedwikibase" delete it
        if (strpos($name, "#unlinkedwikibase") === 0) {
            $new_text = str_replace($old_text_template, '', $new_text);
            continue;
        }
        // ---
        $new_text = check_temps_patterns($name, $old_text_template, $new_text);
        // ---
    };
    // ---
    return $new_text;
}

function remove_lead_templates($text): string
{
    // ---
    // remove any thig before {{Infobox medical condition
    $temps = [
        "{{infobox",
        "{{drugbox",
        "{{speciesbox",
    ];
    // ---
    $text2 = strtolower($text);
    // ---
    foreach ($temps as $temp) {
        $temp_index = strpos($text2, strtolower($temp));
        // ---
        if ($temp_index !== false) {
            $text = substr($text, $temp_index);
            break;
        }
    }

    return trim($text);
}
