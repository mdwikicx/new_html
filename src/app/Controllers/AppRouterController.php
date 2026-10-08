<?PHP
// src/app/Controllers/AppRouterController.php

namespace MDWiki\NewHtml\Controllers;

use function MDWiki\NewHtml\Controllers\main\start;
use MDWiki\NewHtml\Cors;

class AppRouterController
{

    // ------------------------------------------------------------
    // Entry point
    // ------------------------------------------------------------

    public function getContentType(string $printetxt): string
    {
        $content_types = [
            "wikitext" => "text/plain",
            "html"     => "text/html",
            "seg"      => "text/html",
        ];

        return $content_types[$printetxt] ?? "application/json";
    }
    public function setCorsheaders(string $allowedDomain): void
    {
        // Set CORS headers for allowed origins only
        header("Access-Control-Allow-Origin: https://$allowedDomain");
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    private function handleContentType(array $request): void
    {
        $printetxt    = $request['printetxt'] ?? $request['print'] ?? '';
        $content_type = $this->getContentType($printetxt);
        header("Content-type: $content_type; charset=utf-8");
    }

    public function handleRequest(array $request): void
    {
        $this->handleContentType($request);

        $allowedDomain = Cors::is_allowed();

        if (! $allowedDomain) {
            $this->fail(403, 'Access denied. Requests are only allowed from authorized domains.');
        }

        $this->setCorsheaders($allowedDomain);

        $title = $request['title'] ?? '';
        // first litter in $title must be capital
        $title = ucfirst($title);

        if (empty($title)) {
            $this->fail(400, 'title is empty');
        }

        $result = start($request, $title);
        if (is_array($result)) {
            $this->respond($result);
        } else {
            print($result);
        }
        exit(0);
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
