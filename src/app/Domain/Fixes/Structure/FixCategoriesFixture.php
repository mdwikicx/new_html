<?php
/**
 * Category removal utilities
 *
 * Provides functions for removing category links from wikitext.
 *
 * @package MDWiki\NewHtml\Domain\Fixes
 */

namespace MDWiki\NewHtml\Domain\Fixes\Structure;

use MDWiki\NewHtml\Domain\Parser\CategoryParser;

class FixCategoriesFixture
{
    /**
     * Remove all category tags from wikitext
     *
     * @param string $text The wikitext to process
     * @return string The wikitext with categories removed
     */
    public static function removeCategories(string $text): string
    {

        $categories = CategoryParser::get_categories($text);

        foreach ($categories as $name => $cat) {
            // echo "delete category: " . $name . "<br>";
            $text = str_replace($cat, '', $text);
        }

        return $text;
    }
}
