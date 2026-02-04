<?php

/**
 * HtmltoSegments API services
 *
 * Backward compatibility wrapper for Services\Api\SegmentApiService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Api\SegmentApiService instead
 * @package MDWiki\NewHtml\APIServices
 */

namespace APIServices;

use function MDWiki\NewHtml\Services\Api\change_html_to_seg as NewChangeHtmlToSeg;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\change_html_to_seg instead
 */
function change_html_to_seg(string $text): array
{
    return NewChangeHtmlToSeg($text);
}
