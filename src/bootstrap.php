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
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Composer dependencies not installed. Please run: composer install');
}

require_once __DIR__ . '/../vendor/autoload.php';

// Set up error reporting for development
if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Define application paths
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('SRC_PATH')) {
    define('SRC_PATH', __DIR__);
}

if (!defined('REVISIONS_PATH')) {
    $revisions_path = dirname(APP_ROOT) . '/revisions_new';
    if (strpos(__DIR__, 'public_html') !== false) {
        $revisions_path = getenv('HOME') . '/public_html/revisions_new';
    }
    define('REVISIONS_PATH', $revisions_path);
}

// Initialize revisions directory if needed
if (!is_dir(REVISIONS_PATH)) {
    mkdir(REVISIONS_PATH, 0755, true);
}

// Ensure JSON data files exist
$json_file = REVISIONS_PATH . '/json_data.json';
$json_file_all = REVISIONS_PATH . '/json_data_all.json';

if (!defined('JSON_FILE')) {
    define('JSON_FILE', $json_file);
}
if (!defined('JSON_FILE_ALL')) {
    define('JSON_FILE_ALL', $json_file_all);
}
if (!file_exists($json_file)) {
    file_put_contents($json_file, '{}', LOCK_EX);
}

if (!file_exists($json_file_all)) {
    file_put_contents($json_file_all, '{}', LOCK_EX);
}

$user_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';

if (!defined('USER_AGENT')) {
    define('USER_AGENT', $user_agent);
}

// Application is now bootstrapped and ready
