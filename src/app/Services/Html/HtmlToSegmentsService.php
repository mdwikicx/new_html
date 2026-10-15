<?php

declare (strict_types = 1);
/**
 * HTML segmentation services
 *
 * Provides functions for converting HTML to segmented content using
 * the HtmltoSegments API, with caching support.
 *
 * @package MDWiki\NewHtml\Services\Html
 */

namespace MDWiki\NewHtml\Services\Html;

use MDWiki\NewHtml\Infrastructure\Utils\FileUtils;
use MDWiki\NewHtml\Services\Api\SegmentApiService;

class HtmlToSegmentsService
{
    public const REPLACE_URLS = [
        "https://medwiki.toolforge.org/md/"   => "https://en.wikipedia.org/w/",
        "https://medwiki.toolforge.org/w/"    => "https://en.wikipedia.org/w/",
        "https://medwiki.toolforge.org/wiki/" => "https://en.wikipedia.org/wiki/",
    ];

    /** Messages returned by the segmentation service that mean "no content". */
    private const SEG_EMPTY_MESSAGES = [
        'Content for translate is not given or is empty',
        'Sectionwrap: Attempting to remove a non-section tag: undefined',
    ];

    public function __construct(
        private ?SegmentApiService $api = null,
    ) {
        $this->api ??= new SegmentApiService();
    }

    /**
     * Convert HTML to segments with caching support.
     *
     * @param string $text     The HTML text to convert
     * @param string $file_seg The path to the cached segments file
     * @return array{0: string, 1: bool} [segments, fromCache]
     */
    public function load(string $text, string $file_seg): array
    {
        if (! isset($_GET['new'])) {
            $seg_text = FileUtils::readFile($file_seg);

            if (! empty($seg_text)) {
                return [$seg_text, true];
            }
        }

        $result = $this->requestNewContent($text);

        if (empty($result)) {
            return ['', false];
        }

        FileUtils::FileWrite($file_seg, $result);

        return [$result, false];
    }

    /**
     * Convert HTML to segments using the API
     *
     * @param string $text The HTML text to convert
     * @return string The segmented result or empty string on failure
     */
    private function requestNewContent(string $text): string
    {
        $fixed  = $this->api->HtmltoSegments($text);
        $result = $fixed['result'] ?? '';

        return self::modifyTextSegments($result);
    }

    private static function modifyTextSegments(string $text): string
    {
        foreach (self::REPLACE_URLS as $from => $to) {
            $text = str_replace($from, $to, $text);
        }
        if (in_array($text, self::SEG_EMPTY_MESSAGES, true)) {
            $text = '';
        }
        return $text;
    }
}
