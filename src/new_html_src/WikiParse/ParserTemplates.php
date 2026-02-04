<?php

/**
 * MediaWiki template parsing utilities - DEPRECATED
 *
 * This file provides backward compatibility wrappers.
 * New code should use MDWiki\NewHtml\Domain\Parser namespace instead.
 *
 * @package MDWiki\NewHtml\WikiParse
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\TemplateParser instead
 */

namespace WikiParse\Template;

use MDWiki\NewHtml\Domain\Parser\Template as NewTemplate;
use MDWiki\NewHtml\Domain\Parser\ParserTemplate as NewParserTemplate;
use MDWiki\NewHtml\Domain\Parser\ParserTemplates as NewParserTemplates;
use function MDWiki\NewHtml\Domain\Parser\getTemplates as new_getTemplates;

/**
 * Represents a parsed MediaWiki template with its name and parameters
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\Template instead
 */
class Template extends NewTemplate
{
    // Empty class body - all functionality inherited from parent
    // This class exists solely for backward compatibility
}

/**
 * Parser for a single MediaWiki template
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\ParserTemplate instead
 */
class ParserTemplate extends NewParserTemplate
{
    // Empty class body - all functionality inherited from parent
    // This class exists solely for backward compatibility
}

/**
 * Parser for multiple MediaWiki templates in text
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\ParserTemplates instead
 */
class ParserTemplates extends NewParserTemplates
{
    // Empty class body - all functionality inherited from parent
    // This class exists solely for backward compatibility
}

/**
 * Helper function to get all templates from text
 *
 * @param string $text The text to parse
 * @return array<int, Template|MDWiki\NewHtml\Domain\Parser\Template> Array of Template objects
 * @deprecated Use MDWiki\NewHtml\Domain\Parser\getTemplates instead
 */
function getTemplates(string $text): array
{
    return new_getTemplates($text);
}
