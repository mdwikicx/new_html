<?php

/**
 * Bootstrap file for MDWiki NewHtml application
 *
 * This file initializes the application environment, loads the Composer
 * autoloader, and sets up necessary configuration constants.
 *
 * @package MDWiki\NewHtml
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    // Handle the case where the autoload file does not exist
    error_log('Autoload file not found');
    echo ('vendor/autoload.php not found. Please run composer install to set up dependencies.');
    throw new RuntimeException('Autoload file not found');
}

include_once __DIR__ . '/load_env.php';

// Set up error reporting for development
if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

$home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');

if (!defined('REVISIONS_PATH')) {
    $rev_path = getenv('REVISIONS_DIR') ? getenv('REVISIONS_DIR') : (
        $home ? $home . '/public_html/revisions_new' : dirname(__DIR__) . '/revisions'
    );
    define('REVISIONS_PATH', $rev_path);
}

// Initialize revisions directory if needed
if (!is_dir(REVISIONS_PATH)) {
    mkdir(REVISIONS_PATH, 0755, true);
}
