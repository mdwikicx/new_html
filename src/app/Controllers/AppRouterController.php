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

use function MDWiki\NewHtml\Controllers\main\start;
use MDWiki\NewHtml\Cors;

/**
 * HTTP layer only: CORS, validation, status codes, headers, output.
 */
class AppRouterController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

    // ------------------------------------------------------------
    // Entry point
    // ------------------------------------------------------------

    /** @param array<string, mixed> $request */
    public function handleRequest(array $request): void
    {
        $this->handleContentType($request);

        $allowedDomain = Cors::is_allowed();

        if (! $allowedDomain) {
            $this->fail(403, 'Access denied. Requests are only allowed from authorized domains.');
        }

        $this->setCorsHeaders($allowedDomain);

        $title = $this->normalizeTitle((string) ($request['title'] ?? ''));

        if ($title === '') {
            $this->fail(400, 'title is empty');
        }

        $result = start($request, $title);
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

    private function handleContentType(array $request): void
    {
        $printetxt   = $request['printetxt'] ?? $request['print'] ?? '';
        $contentType = $this->getContentType($printetxt);
        header('Content-Type: ' . $contentType . '; charset=utf-8');
    }
    // ------------------------------------------------------------
    // Response helpers
    // ------------------------------------------------------------

    private function respond(string | array $data): void
    {
        // Encode data as JSON with appropriate options
        echo is_array($data)
            ? json_encode($data, self::JSON_FLAGS)
            : $data;
    }

    private function fail(int $statusCode, string $error): never
    {
        http_response_code($statusCode);
        $this->respond(['error' => $error]);
        exit(1);
    }
}
