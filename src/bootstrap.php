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

// Initialize global validator instance for Clean Architecture
// This serves as a simple service locator for backward compatibility
// allowing Domain functions to work without explicit dependency injection
use MDWiki\NewHtml\Services\Api\CommonsImageValidator;

$GLOBALS['imageValidator'] = new CommonsImageValidator();
