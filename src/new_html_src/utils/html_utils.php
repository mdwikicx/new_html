<?PHP
/**
 * HTML fixing utilities
 *
 * Provides functions for fixing and cleaning HTML content, including
 * removing error divs, fixing red links, and removing data-parsoid attributes.
 *
 * @package MDWiki\NewHtml
 */

namespace HtmlFixes;

// Backward compatibility - delegate to new namespace
use function MDWiki\NewHtml\Infrastructure\Utils\del_div_error as new_del_div_error;
use function MDWiki\NewHtml\Infrastructure\Utils\get_attrs as new_get_attrs;
use function MDWiki\NewHtml\Infrastructure\Utils\fix_link_red as new_fix_link_red;
use function MDWiki\NewHtml\Infrastructure\Utils\remove_data_parsoid as new_remove_data_parsoid;

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\del_div_error instead
 */
function del_div_error(string $html): string
{
    return new_del_div_error($html);
}

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\get_attrs instead
 * @return array<string, string>
 */
function get_attrs(string $text): array
{
    return new_get_attrs($text);
}

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\fix_link_red instead
 */
function fix_link_red(string $html): string
{
    return new_fix_link_red($html);
}

/**
 * @deprecated Use MDWiki\NewHtml\Infrastructure\Utils\remove_data_parsoid instead
 */
function remove_data_parsoid(string $html): string
{
    return new_remove_data_parsoid($html);
}
