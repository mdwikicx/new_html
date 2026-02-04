<?php

/**
 * Wiki citation parsing utilities - DEPRECATED
 *
 * This file provides backward compatibility wrappers.
 * New code should use MDWiki\NewHtml\Domain\Parser namespace instead.
 *
 * @package MDWiki\NewHtml\WikiParse
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\CitationsParser instead
 */

namespace WikiParse\Reg_Citations;

use function MDWiki\NewHtml\Domain\Parser\get_ref_name as new_get_ref_name;
use function MDWiki\NewHtml\Domain\Parser\get_regex_citations as new_get_regex_citations;
use function MDWiki\NewHtml\Domain\Parser\get_full_refs as new_get_full_refs;
use function MDWiki\NewHtml\Domain\Parser\get_short_citations as new_get_short_citations;

/*
Usage (DEPRECATED):

use function WikiParse\Reg_Citations\get_ref_name;
use function WikiParse\Reg_Citations\get_regex_citations;
use function WikiParse\Reg_Citations\get_full_refs;
use function WikiParse\Reg_Citations\get_short_citations;

New usage:
use function MDWiki\NewHtml\Domain\Parser\get_ref_name;
use function MDWiki\NewHtml\Domain\Parser\get_regex_citations;
use function MDWiki\NewHtml\Domain\Parser\get_full_refs;
use function MDWiki\NewHtml\Domain\Parser\get_short_citations;

*/

/**
 * Extract the name attribute from a ref tag's options
 *
 * @param string $options The options string from a ref tag
 * @return string The name value, or empty string if not found
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_ref_name instead
 */
function get_ref_name(string $options): string
{
    return new_get_ref_name($options);
}

/**
 * Get all the citations from the provided text and parse them into an array.
 *
 * @param string $text The text containing citations to extract
 * @return array<int, array<string, string>> Array of citation information including content, tag, and options
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_regex_citations instead
 */

function get_regex_citations(string $text): array
{
    return new_get_regex_citations($text);
}

/**
 * Get all full ref tags with name attributes from text
 *
 * @param string $text The text to parse
 * @return array<string, string> Array mapping ref names to their full <ref>...</ref> tags
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_full_refs instead
 */
function get_full_refs(string $text): array
{
    return new_get_full_refs($text);
}

/**
 * Get all short citation tags (self-closing ref tags) from text
 *
 * @param string $text The text to parse
 * @return array<int, array<string, string>> Array of short citation information
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\get_short_citations instead
 */
function get_short_citations(string $text): array
{
    return new_get_short_citations($text);
}
