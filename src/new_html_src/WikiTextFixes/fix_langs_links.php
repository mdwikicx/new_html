<?php

/**
 * Language link removal utilities
 *
 * Provides functions for removing interwiki language links from wikitext.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 */

namespace Fixes\fix_langs_links;

$code_to_lang = [
    "ar" => "Arabic",
    "de" => "German",
    "en" => "English",
    "es" => "Spanish",
    "fr" => "French",
    "it" => "Italian",
    "ja" => "Japanese",
    "nl" => "Dutch",
    "pl" => "Polish",
    "pt" => "Portuguese",
    "ru" => "Russian",
    "sv" => "Swedish",
    "zh" => "Chinese",
];
/**
 * Remove language links from wikitext
 *
 * @param string $text The wikitext to process
 * @return string The wikitext with language links removed
 */
function remove_lang_links(string $text): string
{

    global $code_to_lang;

    // make patern like (ar|en|de)
    $langs = implode('|', array_keys($code_to_lang));

    preg_match_all("/\[\[($langs):[^\]]+\]\]/", $text, $matches);

    foreach ($matches[0] as $link) {
        $text = str_replace($link, '', $text);
    }

    // echo "<pre>";
    // echo htmlentities(var_export($matches, true));
    // echo "</pre><br>";

    return $text;
}
