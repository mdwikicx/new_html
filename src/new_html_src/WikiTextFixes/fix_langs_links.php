<?php

namespace Fixes\fix_langs_links;

/*
Usage:

use function Fixes\fix_langs_links\remove_lang_links;

*/


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
