<?php
/**
 * Autoloader and initialization file for the new_html application
 *
 * This file configures error reporting based on DEBUGX constant
 * and loads the modern bootstrap file with PSR-4 autoloading.
 *
 * @package MDWiki\NewHtml
 */

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Use modern PSR-4 autoloading via bootstrap
require_once __DIR__ . '/bootstrap.php';
