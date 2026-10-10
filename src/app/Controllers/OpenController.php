<?php
namespace MDWiki\NewHtml\Controllers;

/**
 * File viewer for generated content
 *
 * Serves generated files (wikitext, HTML, segments) for a given revision.
 * Validates inputs to prevent path traversal attacks.
 *
 * Request parameters:
 * - revid: Revision ID (must be numeric)
 * - file: File to view (wikitext.txt|html.html|seg.html)
 *
 * @package MDWiki\NewHtml
 */

use MDWiki\NewHtml\Infrastructure\Utils\HtmlUtils;
use MDWiki\NewHtml\Settings;

class OpenController
{
    public string $RevisionsDirPath;

    public function __construct()
    {
        $settings               = Settings::getInstance();
        $this->RevisionsDirPath = $settings->RevisionsDirPath;
    }

    private function handleContentType(string $file): void
    {
        $content_type = ($file == 'wikitext.txt') ? "text/plain" : "text/html";
        header("Content-type: $content_type; charset=utf-8");
    }

    public function handleRequest(array $getRequest): void
    {
        $revid = $getRequest['revid'] ?? '';
        $file  = $getRequest['file'] ?? '';

        // Validate inputs to prevent path traversal
        // revid can be like 1234_all or 1234
        if (! preg_match('/^\d+(_all)?$/', $revid)) {
            $this->fail(400, 'Invalid revision ID');
        }

        $allowed_files = ['wikitext.txt', 'seg.html', 'html.html'];

        if (! in_array($file, $allowed_files, true)) {
            $this->fail(400, 'Invalid file parameter');
        }

        $file_path = $this->RevisionsDirPath . "/$revid/$file";

        if (! is_file($file_path)) {
            $this->fail(404, 'File not found');
        }

        $this->handleContentType($file);

        $text = file_get_contents($file_path) ?: '';

        if (! empty($text)) {
            if ($file == "seg.html" || $file == "html.html") {
                $text = HtmlUtils::removeParsoidData($text);
            }
        }

        echo $text;
    }
    // ------------------------------------------------------------
    // Response helpers
    // ------------------------------------------------------------

    private function respond(array $data): void
    {
        // Encode data as JSON with appropriate options
        print(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function fail(int $statusCode, string $error): never
    {
        http_response_code($statusCode);
        $this->respond(['error' => $error]);
        exit(1);
    }
}
