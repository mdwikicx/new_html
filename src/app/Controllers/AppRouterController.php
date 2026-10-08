<?PHP

// src/app/Controllers/AppRouterController.php

namespace MDWiki\NewHtml\Controllers;

/**
 * Route handler for new_html application
 *
 * Routes incoming requests to the appropriate handler:
 * - Empty requests or ?test -> redirect to revisions.html (dashboard)
 * - Requests with parameters -> main.php (API endpoint)
 *
 * @package MDWiki\NewHtml
 *
 * Test at: http://localhost:305/new_html_1/revisions.html
 */

use MDWiki\NewHtml\Cors;
use MDWiki\NewHtml\DTO\PageRequest;
use MDWiki\NewHtml\DTO\PageResult;
use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Services\Pipeline\PagePipelineService;

/**
 * HTTP layer only: CORS, validation, status codes, headers, output.
 */
class AppRouterController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

    private PagePipelineService $pipeline;

    public function __construct(?PagePipelineService $pipeline = null)
    {
        $this->pipeline = $pipeline ?? new PagePipelineService();
    }

    // ------------------------------------------------------------
    // Entry point
    // ------------------------------------------------------------

    /** @param array<string, mixed> $getRequest */
    public function handleRequest(array $getRequest): void
    {
        $allowedDomain = Cors::isAllowed($_SERVER);

        if (! $allowedDomain) {
            $this->fail(403, 'Access denied. Requests are only allowed from authorized domains.');
        }

        $this->setCorsHeaders($allowedDomain);

        $title = $this->normalizeTitle((string) ($getRequest['title'] ?? ''));

        if ($title === '') {
            $this->fail(400, 'title is empty');
        }

        try {
            $result = $this->pipeline->process(PageRequest::fromArray($getRequest, $title));
        } catch (\Throwable $e) {
            Logger::error("Unhandled error for title: $title. Error: " . $e->getMessage());
            $this->fail(500, 'Internal server error');
        }

        $this->respond($result);
    }

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------

    public function getContentType(string $format): string
    {
        return match ($format) {
            'wikitext' => 'text/plain',
            'html'     => 'text/html',
            'seg'      => 'text/html',
            default    => 'application/json',
        };
    }

    public function setCorsHeaders(string $allowedDomain): void
    {
        // Set CORS headers for allowed origins only
        header("Access-Control-Allow-Origin: https://$allowedDomain");
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    /** Uppercase the first character (UTF-8 safe). */
    private function normalizeTitle(string $title): string
    {
        if ($title === '') {
            return '';
        }
        return mb_strtoupper(mb_substr($title, 0, 1)) . mb_substr($title, 1);
    }

    // ------------------------------------------------------------
    // Response helpers
    // ------------------------------------------------------------

    private function respond(PageResult $result): void
    {
        http_response_code($result->status);
        header('Content-Type: ' . $this->getContentType($result->format) . '; charset=utf-8');

        // Encode data as JSON with appropriate options
        $data = $result->body;
        echo is_array($data)
            ? json_encode($data, self::JSON_FLAGS)
            : $data;
    }

    private function fail(int $statusCode, string $error): never
    {
        $this->respond(PageResult::json($statusCode, ['error' => $error]));
        exit(1);
    }
}
