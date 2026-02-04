<?php

namespace WikiParse\Reg_Citations;

/*
Usage:

use function WikiParse\Reg_Citations\get_ref_name;
use function WikiParse\Reg_Citations\get_regex_citations;
use function WikiParse\Reg_Citations\get_full_refs;
use function WikiParse\Reg_Citations\get_short_citations;

*/

/**
 * Extract the name attribute from a ref tag's options
 *
 * @param string $options The options string from a ref tag
 * @return string The name value, or empty string if not found
 */
function get_ref_name(string $options): string
{
    if (empty(trim($options))) {
        return "";
    }
    // $pa = "/name\s*=\s*\"(.*?)\"/i";
    $pa = "/name\s*\=\s*[\"\']*([^>\"\']*)[\"\']*\s*/i";
    preg_match($pa, $options, $matches);

    if (!isset($matches[1])) {
        return "";
    }
    $name = trim($matches[1]);
    return $name;
}

/**
 * Get all the citations from the provided text and parse them into an array.
 *
 * @param string $text The text containing citations to extract
 * @return array<int, array<string, string>> Array of citation information including content, tag, and options
 */

function get_regex_citations(string $text): array
{
    preg_match_all("/<ref([^\/>]*?)>(.+?)<\/ref>/is", $text, $matches);

    $citations = [];

    foreach ($matches[1] as $key => $citation_options) {
        $content = $matches[2][$key];
        $ref_tag = $matches[0][$key];
        $options = $citation_options;
        $citation = [
            "content" => $content,
            "tag" => $ref_tag,
            "name" => get_ref_name($options),
            "options" => $options
        ];
        $citations[] = $citation;
    }    return $citations;
}

/**
 * Get all full ref tags with name attributes from text
 *
 * @param string $text The text to parse
 * @return array<string, string> Array mapping ref names to their full <ref>...</ref> tags
 */
function get_full_refs(string $text): array
{
    $full = [];
    $citations = get_regex_citations($text);

    foreach ($citations as $cite) {
        $name = $cite["name"];
        $ref = $cite["tag"];

        if (empty($name)) {
            continue;
        }

        $full[$name] = $ref;
    };
    return $full;
}

/**
 * Get all short citation tags (self-closing ref tags) from text
 *
 * @param string $text The text to parse
 * @return array<int, array<string, string>> Array of short citation information
 */
function get_short_citations(string $text): array
{
    preg_match_all("/<ref ([^\/>]*?)\/\s*>/is", $text, $matches);

    $citations = [];

    foreach ($matches[1] as $key => $citation_options) {
        $ref_tag = $matches[0][$key];
        $options = $citation_options;
        $citation = [
            "content" => "",
            "tag" => $ref_tag,
            "name" => get_ref_name($options),
            "options" => $options
        ];
        $citations[] = $citation;
    }

    return $citations;
}
