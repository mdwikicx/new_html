<?php
namespace MDWiki\NewHtml\Services\Pipeline;

/**
 * Orchestrates: fetch wikitext -> fix -> HTML -> segments.
 * This file handles HTTP requests to generate page content from MDWiki.
 * It orchestrates the full pipeline: fetching wikitext, applying fixes,
 * converting to HTML, and segmenting the content.
 *
 * Request parameters:
 * - title: Page title to process
 * - new: Force regeneration of cached content
 * - all: Use 'all' data file for Video pages
 * - printetxt: Output format (wikitext|html|seg)
 * - print: Alias for printetxt
 *
 * @package MDWiki\NewHtml
 */

use MDWiki\NewHtml\Controllers\JsonDataController;
use MDWiki\NewHtml\DTO\PageRequest;
use MDWiki\NewHtml\DTO\PageResult;
use MDWiki\NewHtml\Handlers\WikitextHandler;
use MDWiki\NewHtml\Infrastructure\Utils\FileUtils;
use MDWiki\NewHtml\Infrastructure\Utils\HtmlUtils;
use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Services\Html\HtmlToSegmentsService;
use MDWiki\NewHtml\Services\Html\WikitextToHtmlService;
use MDWiki\NewHtml\Services\Wikitext\WikitextFixerService;
use MDWiki\NewHtml\Settings;

final class PagePipelineService
{
    /** Messages returned by the segmentation service that mean "no content". */
    private const SEG_EMPTY_MESSAGES = [
        'Content for translate is not given or is empty',
        'Sectionwrap: Attempting to remove a non-section tag: undefined',
    ];

    private WikitextFixerService $fixer;
    private WikitextToHtmlService $htmlService;
    private string $jsonFile;
    private string $jsonFileAll;

    public function __construct(
        ?WikitextFixerService $fixer = null,
        ?WikitextToHtmlService $htmlService = null,
        ?string $jsonFile = null,
        ?string $jsonFileAll = null,
    ) {
        $this->fixer       = $fixer ?? new WikitextFixerService();
        $this->htmlService = $htmlService ?? new WikitextToHtmlService();

        $settings          = Settings::getInstance();
        $this->jsonFile    = $jsonFile ?? $settings->jsonFile;
        $this->jsonFileAll = $jsonFileAll ?? $settings->jsonFileAll;
    }

    // ------------------------------------------------------------
    // Entry point
    // ------------------------------------------------------------

    public function process(PageRequest $req): PageResult
    {
        $cache = ['wikitext' => false, 'html' => false, 'seg' => false];

        [$wikitext, $revision, $cache['wikitext']] = $this->fetchWikitext($req);

        if ($req->format === PageRequest::FORMAT_WIKITEXT) {
            // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=wikitext
            $text = $this->fixer->fix($wikitext, $req->title);
            return PageResult::text(
                PageRequest::FORMAT_WIKITEXT,
                $text
            );
        }
        // $revision = (isset($getRequest['revision'])) ? $getRequest['revision'] : $revision;

        if (empty($wikitext) || empty($revision)) {
            return $this->notFound($req->title, $revision);
        }

        $dir      = FileUtils::get_file_dir($revision, $req->all);
        $wikitext = $this->fixer->fix($wikitext, $req->title);

        FileUtils::FileWrite($dir . '/wikitext.txt', $wikitext);
        FileUtils::FileWrite($dir . '/title.txt', $req->title);

        try {
            [$html, $cache['html']] = $this->buildHtml($wikitext, $dir . '/html.html', $req);
        } catch (\Throwable $e) {
            Logger::error("HTML generation failed for title: {$req->title}. Error: " . $e->getMessage());
            Logger::debug("HTML generation failed for title: {$req->title}. Error: " . $e->getMessage());
            return PageResult::json(
                500,
                ['error' => 'Failed to generate HTML content']
            );
        }

        if ($req->format === PageRequest::FORMAT_HTML) {
            // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=html
            return PageResult::text(PageRequest::FORMAT_HTML, $html);
        }

        $data = $this->baseData($req->title, $revision, $cache);

        if (empty($html)) {
            // NOTE: status 200 kept for backward compatibility with the old main.php
            $data['error_type'] = 'HTML_text:() is empty';
            $data['error']      = 'No content found';
            return PageResult::json(200, $data);
        }

        [$seg, $cache['seg']] = $this->buildSegments($html, $dir . '/seg.html');

        if ($req->format === PageRequest::FORMAT_SEG) {
            // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=seg
            return PageResult::text(PageRequest::FORMAT_SEG, $seg);
        }

        $data['cache_data']       = $cache;
        $data['segmentedContent'] = $seg;

        if (empty($seg)) {
            // send request error code using http_response_code
            $data['error_type'] = 'SEG_text:() is empty';
            $data['error']      = 'No content found';
            return PageResult::json(404, $data);
        }

        return PageResult::json(200, $data);
    }

    // ------------------------------------------------------------
    // Steps
    // ------------------------------------------------------------

    /**
     * Get wikitext and revision ID for a page, either from API or cache
     *
     * @param PageRequest $req
     *  string $req->all The page title to fetch
     *  string $req->all Whether to use 'all' data file (non-empty) or main file (empty)
     * @return array{0: string, 1: string, 2: bool} [wikitext, revision, fromCache]
     */
    private function fetchWikitext(PageRequest $req): array
    {
        if (empty($req->all)) {
            $json = WikitextHandler::getWikitext($req->title, $this->jsonFile, true);
            $file = $this->jsonFile;
        } else {
            $json = WikitextHandler::getWikitext($req->title, $this->jsonFileAll);
            $file = $this->jsonFileAll;
        }

        $wikitext = $json['source'];
        $revision = $json['revid'];

        if (empty($wikitext) || empty($revision)) {
            [$wikitext, $revision] = $this->fromLocalCache($req->title, $req->all, $file);
            return [$wikitext, $revision, ! empty($wikitext)];
        }

        return [$wikitext, $revision, false];
    }

    /**
     * Get wikitext and revision ID from cached JSON data
     *
     * @param string $title The page title
     * @param string $file
     * @return array{0: string, 1: string} [wikitext, revision]
     */
    private function fromLocalCache(string $title, string $all, string $file): array
    {
        $revid = JsonDataController::getTitleRevision($title, $file);

        if (empty($revid) || ! ctype_digit((string) $revid)) {
            return ['', ''];
        }

        $dir = FileUtils::get_file_dir($revid, $all);

        if (! is_dir($dir)) {
            return ['', ''];
        }

        return [FileUtils::readFile($dir . '/wikitext.txt'), $revid];
    }

    /**
     * Convert wikitext to HTML with caching support
     *
     * @param string $wikitext The wikitext to convert
     * @param string $fileHtml The path to the cached HTML file
     * @param PageRequest $req
     * @return array{0: string, 1: bool} [html, fromCache]
     * @throws \Throwable when conversion fails
     */
    private function buildHtml(string $wikitext, string $fileHtml, PageRequest $req): array
    {
        [$html, $fromCache] = $this->htmlService->convertWithCache($wikitext, $fileHtml, $req->title, $req->new);

        $html = HtmlUtils::remove_data_parsoid($html);

        if ($html === $wikitext) {
            $html = '';
        }

        return [$html, $fromCache];
    }

    /**
     * Convert HTML to segments with caching support
     *
     * @param string $html The HTML text to convert to segments
     * @param string $fileSeg The path to the cached segments file
     * @return array{0: string, 1: bool} [segments, fromCache]
     */
    private function buildSegments(string $html, string $fileSeg): array
    {
        [$seg, $fromCache] = HtmlToSegmentsService::html_to_seg($html, $fileSeg);

        $seg = HtmlUtils::remove_data_parsoid($seg);

        if (in_array($seg, self::SEG_EMPTY_MESSAGES, true)) {
            $seg = '';
        }

        return [$seg, $fromCache];
    }

    // ------------------------------------------------------------
    // Result builders
    // ------------------------------------------------------------

    /**
     * @param array<string, bool> $cache
     * @param string $title
     * @param string $revision
     * @return array<string, mixed>
     */
    private function baseData(string $title, string $revision, array $cache): array
    {
        return [
            'cache_data'       => $cache,
            'sourceLanguage'   => 'en',
            'title'            => $title,
            'revision'         => $revision,
            'segmentedContent' => '',
            'categories'       => [],
        ];
    }

    private function notFound(string $title, string $revision): PageResult
    {
        return PageResult::json(404, [
            'sourceLanguage'   => 'en',
            'title'            => $title,
            'revision'         => $revision,
            'segmentedContent' => '',
            'categories'       => [],
            'error_type'       => "title:($title) or revision:($revision) not found",
            'error'            => 'No content found!',
        ]);
    }

}
