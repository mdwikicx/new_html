<?php

/**
 * HTML segmentation services
 *
 * Backward compatibility wrapper for Services\Html\HtmlToSegmentsService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Html\HtmlToSegmentsService instead
 * @package MDWiki\NewHtml
 */

namespace Segments;

use function MDWiki\NewHtml\Services\Html\do_html_to_seg as NewDoHtmlToSeg;
use function MDWiki\NewHtml\Services\Html\html_to_seg as NewHtmlToSeg;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Html\do_html_to_seg instead
 */
function do_html_to_seg(string $text): string
{
    return NewDoHtmlToSeg($text);
}

/**
 * @deprecated Use MDWiki\NewHtml\Services\Html\html_to_seg instead
 * @return array{0: string, 1: bool}
 */
function html_to_seg(string $text, string $file_seg): array
{
    return NewHtmlToSeg($text, $file_seg);
}
