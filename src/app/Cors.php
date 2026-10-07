<?PHP
namespace MDWiki\NewHtml\Cors;

const ALLOWED_DOMAINS = [
    'mdwikicx.toolforge.org',
    'mdwiki.toolforge.org',
    'medwiki.toolforge.org',
];

function is_allowed()
{

    $env = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? '');

    if ($env === 'development') {
        return true;
    }

    $domains = ['medwiki.toolforge.org', 'mdwikicx.toolforge.org'];
    // Check if the request is coming from allowed domains
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $origin  = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

    $isAllowed = false;
    foreach ($domains as $domain) {
        if (strpos($referer, $domain) !== false || strpos($origin, $domain) !== false) {
            $isAllowed = $domain;
            break;
        }
    }

    // log $_SERVER to file
    // file_put_contents(__DIR__ . '/cors.log', print_r($_SERVER, true));

    return $isAllowed;
}

/**
 * Set CORS headers for allowed domains
 *
 * Checks the Origin header against allowed domains and sets appropriate CORS headers.
 *
 * @return void
 */
function set_cors_headers(): void
{

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    // Reject OPTIONS requests without origin
    if (! $origin) {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(400);
            exit;
        }
        return;
    }

    $origin_host = parse_url($origin, PHP_URL_HOST);

    // Reject unauthorized origins
    if (! in_array($origin_host, ALLOWED_DOMAINS, true)) {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(403);
            exit;
        }
        return;
    }

    // Set CORS headers for allowed origins only
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
