<?php

/**
 * Autoloader and initialization file for the new_html application
 *
 * This file configures error reporting based on DEBUGX constant
 * and includes all necessary source files for the application.
 *
 * @package MDWiki\NewHtml
 */

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('REVISIONS_PATH')) {
    define('REVISIONS_PATH', APP_ROOT . '/../revisions_new');
}

include_once __DIR__ . '/new_html_src/require.php';
