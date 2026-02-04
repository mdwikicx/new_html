<?php

/**
 * Image and video removal utilities
 *
 * DEPRECATED: This file is kept for backward compatibility.
 * Please use MDWiki\NewHtml\Domain\Fixes\Media\FixImagesFixture instead.
 *
 * @package MDWiki\NewHtml\WikiTextFixes
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media namespace instead
 */

namespace Fixes\FixImages;

use function MDWiki\NewHtml\Domain\Fixes\Media\remove_images as new_remove_images;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_videos as new_remove_videos;

/*
Usage:

use function Fixes\FixImages\remove_images;
use function Fixes\FixImages\remove_videos;

*/

/**
 * Remove image tags from wikitext by wrapping them in conditional existence check
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media\remove_images instead
 * @param string $text The wikitext to process
 * @return string The wikitext with images wrapped in {{subst:#ifexist:...}}
 */
function remove_images(string $text): string
{
    return new_remove_images($text);
}

/**
 * Remove video file tags from wikitext
 *
 * @deprecated Use MDWiki\NewHtml\Domain\Fixes\Media\remove_videos instead
 * @param string $text The wikitext to process
 * @return string The wikitext with video files removed
 */
function remove_videos(string $text): string
{
    return new_remove_videos($text);
}
