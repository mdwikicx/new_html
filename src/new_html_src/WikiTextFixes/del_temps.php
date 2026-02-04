<?php

/**
 * Template removal utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\Templates\DeleteTemplatesFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Templates namespace instead
 */

namespace Fixes\DelTemps;

use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_templates as new_remove_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_lead_templates as new_remove_lead_templates;

/*
Usage:

use function Fixes\DelTemps\remove_templates;
use function Fixes\DelTemps\remove_lead_templates;

*/

/**
 * Remove unwanted templates from wikitext
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Templates\remove_templates instead
 * @param string $text The wikitext to process
 * @return string The wikitext with unwanted templates removed
 */
function remove_templates(string $text): string
{
    return new_remove_templates($text);
}

/**
 * Remove content before infobox templates in lead section
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Templates\remove_lead_templates instead
 * @param string $text The wikitext to process
 * @return string The wikitext with content before infobox removed
 */
function remove_lead_templates(string $text): string
{
    return new_remove_lead_templates($text);
}
