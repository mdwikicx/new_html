<?php

use MDWiki\NewHtml\Controllers\AppRouterController;

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

$env = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'development');

if ($env === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

include_once __DIR__ . "/bootstrap.php";

if ((empty($_GET) && empty($_POST)) || (count($_GET) == 1 && isset($_GET["test"]))) {
    // require_once __DIR__ . "/revisions.html";
    header("Location: revisions.html");
} else {
    $controller = new AppRouterController();
    $controller->handleRequest($_GET);
}
