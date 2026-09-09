<?php

/**
 * Source file loader for Domain module
 *
 * This file loads all necessary source files for the Domain module,
 * including parsing utilities, API services, text fixes, HTML services,
 * and helper utilities. It uses a mix of require_once for core files
 * and glob patterns for extensibility.
 *
 * @package MDWiki\NewHtml
 */
$src_path = __DIR__ . '/src/';

if (!is_dir($src_path)) {
    $src_path = __DIR__ . '/../src/';
}

require_once __DIR__ . "/utils.php";
require_once $src_path . "/bootstrap.php";
