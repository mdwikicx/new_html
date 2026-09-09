<?php

/**
 * Route handler for new_html application
 *
 * Routes incoming requests to the appropriate handler:
 * - Empty requests or ?test -> redirect to revisions.html (dashboard)
 * - Requests with parameters -> main.php (API endpoint)
 *
 * @package MDWiki\NewHtml
 */

function get_content_type(string $printetxt): string
{
    $content_types = [
        "wikitext" => "text/plain",
        "html" => "text/html",
        "seg" => "text/html",
    ];

    return $content_types[$printetxt] ?? "application/json";
}

if ((empty($_GET) && empty($_POST)) || (count($_GET) == 1 && isset($_GET["test"]))) {
    // require_once __DIR__ . "/revisions.html";
    header("Location: revisions.html");
} else {
    $printetxt = $_GET['printetxt'] ?? $_GET['print'] ?? '';
    $content_type = get_content_type($printetxt);
    header("Content-type: $content_type");

    require_once __DIR__ . "/main.php";
}
