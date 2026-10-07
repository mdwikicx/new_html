<?php

/**
 * HTML conversion services
 *
 * Provides functions for converting wikitext to HTML using the
 * Wikipedia REST API, with caching support.
 *
 * @package MDWiki\NewHtml\Services\Html
 */

namespace MDWiki\NewHtml\Services\Html;

use function MDWiki\NewHtml\Infrastructure\Utils\fix_link_red;
use function MDWiki\NewHtml\Infrastructure\Utils\del_div_error;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;
use MDWiki\NewHtml\Services\Api\TransformApiService;

class WikitextToHtmlService
{
    private TransformApiService $transformService;

    /**
     * @param TransformApiService|null $transformService Optional injected service (useful for testing)
     */
    public function __construct(?TransformApiService $transformService = null)
    {
        $this->transformService = $transformService ?? new TransformApiService();
    }

    /**
     * Convert wikitext to HTML using the API and apply fixes
     *
     * @param string $wikitext The wikitext to convert
     * @param string $title The page title for context
     * @return string The HTML result or empty string on failure
     */
    public function convert(string $wikitext, string $title): string
    {
        if (empty($wikitext)) {
            return "";
        }

        $title = str_replace(" ", "_", $title);

        $fixed = $this->transformService->convert($wikitext, $title);

        $result = $fixed['result'] ?? '';

        if (empty($result)) {
            return "";
        }

        $result = del_div_error($result);
        $result = fix_link_red($result);

        return $result;
    }

    /**
     * Convert wikitext to HTML with caching support
     *
     * @param string $wikitext The wikitext to convert
     * @param string $file_html The path to the cached HTML file
     * @param string $title The page title for context
     * @param bool $new Whether to force regeneration (true) or use cache (false)
     * @return array{0: string, 1: bool} Array containing [html_content, from_cache]
     */
    public function convertWithCache(string $wikitext, string $file_html, string $title, bool $new): array
    {
        if (!$new) {
            $text = read_file($file_html);
            if (!empty($text)) {
                return [$text, true];
            }
        }

        if (empty($wikitext)) {
            return ["", false];
        }

        $result = $this->convert($wikitext, $title);

        if (empty($result)) {
            return ["", false];
        }

        file_write($file_html, $result);

        return [$result, false];
    }
}
