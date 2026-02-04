<?php

/**
 * Empty reference handling utilities
 *
 * Provides functions for deleting or expanding empty short reference tags
 * in wikitext.
 *
 * @package MDWiki\NewHtml\Domain\Fixes\References
 */

namespace MDWiki\NewHtml\Domain\Fixes\References;

/*
Usage:

use function MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs;

*/


use function MDWiki\NewHtml\Domain\Parser\get_full_refs;
use function MDWiki\NewHtml\Domain\Parser\get_short_citations;

/**
 * Delete empty short refs or expand them with full ref definitions
 *
 * @param string $first The text containing short refs
 * @return string The text with empty refs removed and expandable refs replaced
 */
function del_empty_refs(string $first): string
{

    $refs = get_full_refs($first);
    // echo  "refs:" . count($refs) . "<br>";

    $short_refs = get_short_citations($first);
    // echo  "short_refs:" . count($short_refs) . "<br>";

    foreach ($short_refs as $cite) {
        $name = $cite["name"];
        $refe = $cite["tag"];

        $rr = $refs[$name] ?? false;
        if ($rr) {
            // if $rr already in $first : continue
            if (strpos($first, $rr) === false) {
                $first = str_replace($refe, $rr, $first);
            }
        } else {
            $first = str_replace($refe, "", $first);
        }
    }
    return $first;
}
