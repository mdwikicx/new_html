<?php
/**
 * Route handler for new_html application
 *
 * Routes incoming requests to the appropriate handler:
 * - Empty requests or ?test -> redirect to revisions.php (dashboard)
 * - Requests with parameters -> main.php (API endpoint)
 *
 * @package MDWiki\NewHtml
 */

// http://localhost:14/new_html/

if ((empty($_GET) && empty($_POST)) || (count($_GET) == 1 && isset($_GET["test"]))) {
    // require_once __DIR__ . "/revisions.php";
    header("Location: revisions.php");
} else {
    require_once __DIR__ . "/main.php";
}
