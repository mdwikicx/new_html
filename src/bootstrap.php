<?php

/**
 * Autoloader and initialization file for the new_html application
 *
 * This file configures error reporting based on DEBUGX constant
 * and includes all necessary source files for the application.
 *
 * @package MDWiki\NewHtml
 */

// Set up error reporting for development
if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

if (!defined('USER_AGENT')) {
    $user_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';
    define('USER_AGENT', $user_agent);
}

// Define application paths
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('SRC_PATH')) {
    define('SRC_PATH', __DIR__);
}

if (!defined('REVISIONS_PATH')) {
    $rev_path = dirname(APP_ROOT) . '/revisions_new';
    if (strpos(__DIR__, 'public_html') !== false) {
        $rev_path = getenv('HOME') . '/public_html/revisions_new';
    }
    define('REVISIONS_PATH', $rev_path);
}

// Ensure JSON data files exist

if (!defined('JSON_FILE')) {
    $json_file = REVISIONS_PATH . '/json_data.json';
    define('JSON_FILE', $json_file);
}
if (!defined('JSON_FILE_ALL')) {
    $json_file_all = REVISIONS_PATH . '/json_data_all.json';
    define('JSON_FILE_ALL', $json_file_all);
}

// Initialize revisions directory if needed
if (!is_dir(REVISIONS_PATH)) {
    mkdir(REVISIONS_PATH, 0755, true);
}

if (!file_exists(JSON_FILE)) {
    file_put_contents(JSON_FILE, '{}', LOCK_EX);
}

if (!file_exists(JSON_FILE_ALL)) {
    file_put_contents(JSON_FILE_ALL, '{}', LOCK_EX);
}


include_once __DIR__ . '/new_html_src/require.php';
