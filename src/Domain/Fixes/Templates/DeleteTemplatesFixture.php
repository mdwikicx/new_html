<?php

/**
 * Template removal utilities
 *
 * Provides functions for removing unwanted templates from wikitext,
 * including maintenance templates, stub templates, and other
 * non-essential templates.
 *
 * @package MDWiki\NewHtml\Domain\Fixes\Templates
 */

namespace MDWiki\NewHtml\Domain\Fixes\Templates;

use function MDWiki\NewHtml\Domain\Parser\getTemplates;

/**
 * Check if a template matches deletion patterns and remove it
 *
 * @param string $name The template name (lowercase)
 * @param string $old_text_template The original template text
 * @param string $new_text The current text being processed
 * @return string The text with template removed if it matches patterns
 */
function check_temps_patterns(string $name, string $old_text_template, string $new_text): string
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

    foreach ($temps_patterns as $pattern) {
        if (preg_match($pattern, $name)) {
            $new_text = str_replace($old_text_template, '', $new_text);
            break;
        }
    }
    return $new_text;
}

/**
 * Check if a template should be deleted based on its name
 *
 * @param string $name The template name (lowercase)
 * @return bool True if template should be deleted, false otherwise
 */
function check_temp_to_delete(string $name): bool
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
    return in_array($name, $tempsToDelete);
}

/**
 * Remove unwanted templates from wikitext
 *
 * @param string $text The wikitext to process
 * @return string The wikitext with unwanted templates removed
 */
function remove_templates(string $text): string
{
    $temps_in = getTemplates($text);

    $new_text = $text;

    foreach ($temps_in as $temp) {

        $name = strtolower($temp->getStripName());

        $old_text_template = $temp->getTemplateText();

        if (check_temp_to_delete($name)) {
            $new_text = str_replace($old_text_template, '', $new_text);
            continue;
        }
        // ---        // if $name start with "#unlinkedwikibase" delete it
        if (strpos($name, "#unlinkedwikibase") === 0) {
            $new_text = str_replace($old_text_template, '', $new_text);
            continue;
        }

        $new_text = check_temps_patterns($name, $old_text_template, $new_text);
    };
    return $new_text;
}

/**
 * Remove content before infobox templates in lead section
 *
 * @param string $text The wikitext to process
 * @return string The wikitext with content before infobox removed
 */
function remove_lead_templates(string $text): string
{

    // remove any thig before {{Infobox medical condition
    $temps = [
        "{{infobox",
        "{{drugbox",
        "{{speciesbox",
    ];

    $text2 = strtolower($text);

    foreach ($temps as $temp) {
        $temp_index = strpos($text2, strtolower($temp));

        if ($temp_index !== false) {
            $text = substr($text, $temp_index);
            break;
        }
    }

    return trim($text);
}
