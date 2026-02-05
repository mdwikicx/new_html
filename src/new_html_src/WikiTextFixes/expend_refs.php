<?php

/**
 * Reference expansion utilities
 *
 * Provides functions for expanding short references (named ref tags)
 * by finding their full definitions elsewhere in the text.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 */

namespace MDWiki\NewHtml\Domain\Fixes\References;

use function MDWiki\NewHtml\Domain\Parser\get_full_refs;
use function MDWiki\NewHtml\Domain\Parser\get_short_citations;
use function Printn\test_print;

/**
 * Expand short references by finding their full definitions in the text
 *
 * @param string $first The lead section text with short refs
 * @param string $alltext The full page text containing full ref definitions
 * @return string The text with short refs expanded to full refs
 */
function refs_expend_work(string $first, string $alltext): string
{
    if (empty($alltext)) {
        $alltext = $first;
    }

    test_print("refs_expend_work: \n");

    $allpage_fullrefs = get_full_refs($alltext);

    $lead_fullrefs = get_full_refs($first);
    $lead_short_refs = get_short_citations($first);

    test_print(var_export($lead_short_refs, true));

    foreach ($lead_short_refs as $cite) {

        $name = $cite["name"] ?? '';
        $refe = $cite["tag"] ?? '';

        if (empty($name) || empty($refe)) {
            continue;
        }

        if (isset($lead_fullrefs[$name])) {
            continue;
        }

        $rr = $allpage_fullrefs[$name] ?? "";

        if (!empty($rr)) {
            test_print("refs_expend_work: name:($name), refe:($refe), rr:($rr)\n");
            $first = str_replace($refe, $rr, $first);
        }
    }

    return $first;
}
