<?php

/**
 * Reference expansion utilities
 *
 * Provides functions for expanding short references (named ref tags)
 * by finding their full definitions elsewhere in the text.
 *
 * @package MDWiki\NewHtml\Domain\Fixes\References
 */

namespace MDWiki\NewHtml\Domain\Fixes\References;

use MDWiki\NewHtml\Domain\Parser\CitationsParser;
use MDWiki\NewHtml\Logger;

class ExpandRefsFixture
{
    /**
     * Expand short references by finding their full definitions in the text
     *
     * @param string $first The lead section text with short refs
     * @param string $alltext The full page text containing full ref definitions
     * @return string The text with short refs expanded to full refs
     */
    public static function expand_text_refs(string $first, string $alltext): string
    {
        if (empty($alltext)) {
            $alltext = $first;
        }

        Logger::debug("expand_text_refs: \n");

        $allpage_fullrefs = CitationsParser::get_full_refs($alltext);

        $lead_fullrefs   = CitationsParser::get_full_refs($first);
        $lead_short_refs = CitationsParser::get_short_citations($first);

        Logger::debug(var_export($lead_short_refs, true));

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

            if (! empty($rr)) {
                Logger::debug("expand_text_refs: name:($name), refe:($refe), rr:($rr)\n");
                $first = str_replace($refe, $rr, $first);
            }
        }

        return $first;
    }
}
