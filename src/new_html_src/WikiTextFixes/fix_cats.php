<?php
/**
 * Category removal utilities
 *
 * Provides functions for removing category links from wikitext.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 */

namespace Fixes\FixCats;

use function WikiParse\Category\get_categories;

/**
 * Remove all category tags from wikitext
 *
 * @param string $text The wikitext to process
 * @return string The wikitext with categories removed
 */
function remove_categories(string $text): string
{

    $categories = get_categories($text);

    foreach ($categories as $name => $cat) {
        // echo "delete category: " . $name . "<br>";
        $text = str_replace($cat, '', $text);
    }

    return $text;
}
