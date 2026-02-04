<?php
/**
 * Missing image removal utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\Media\RemoveMissingImagesFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media namespace instead
 */

namespace RemoveMissingImages;

use function MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_infobox_images as new_remove_missing_infobox_images;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_inline_images as new_remove_missing_inline_images;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_images as new_remove_missing_images;

/*
usage:

use function RemoveMissingImages\check_commons_image_exists;
use function RemoveMissingImages\remove_missing_infobox_images;
use function RemoveMissingImages\remove_missing_inline_images;
use function RemoveMissingImages\remove_missing_images;

*/

/**
 * Remove infobox images that don't exist on Commons
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_infobox_images instead
 * @param string $text The wikitext to process
 * @return string The processed wikitext
 */
function remove_missing_infobox_images(string $text): string
{
    return new_remove_missing_infobox_images($text);
}

/**
 * Remove inline [[File:...]] or [[Image:...]] images that don't exist on Commons
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_inline_images instead
 * @param string $text The wikitext to process
 * @return string The processed wikitext
 */
function remove_missing_inline_images(string $text): string
{
    return new_remove_missing_inline_images($text);
}

/**
 * Main function: Remove all missing images (both infobox and inline)
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_images instead
 * @param string $text The wikitext to process
 * @return string The processed wikitext with missing images removed
 */
function remove_missing_images(string $text): string
{
    return new_remove_missing_images($text);
}
