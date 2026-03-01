<?php

/**
 * Source file loader for MDWiki NewHtml application
 *
 * This file uses PSR-4 autoloading via Composer for all MDWiki\NewHtml
 * namespaced classes and functions. Manual requires are only needed
 * for files outside the autoloaded namespace.
 *
 * @package MDWiki\NewHtml
 */

// Load utils.php which has a different namespace (MDWiki\NewHtmlMain\Utils)
require_once __DIR__ . "/utils.php";

// Load the src/bootstrap.php for application constants
$src_path = __DIR__ . '/src/';
if (!is_dir($src_path)) {
    $src_path = __DIR__ . '/../src/';
}
require_once $src_path . "/bootstrap.php";

// All other files are loaded via PSR-4 autoloading configured in composer.json
// Classes: Autoloaded when first used via "use" statements
// Functions: Loaded via "files" autoload in composer.json
