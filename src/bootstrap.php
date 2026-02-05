<?php

/**
 * Bootstrap file for MDWiki NewHtml application
 *
 * This file initializes the application environment, loads the Composer
 * autoloader, and sets up necessary configuration constants.
 *
 * @package MDWiki\NewHtml
 */

if (!defined('USER_AGENT')) {
    $user_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';
    define('USER_AGENT', $user_agent);
}

// Define application paths
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

$home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');

if (!defined('REVISIONS_PATH')) {
    $rev_path = getenv('REVISIONS_DIR') ? getenv('REVISIONS_DIR') : (
        $home ? $home . '/public_html/revisions_new' : APP_ROOT . '/revisions'
    );
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
