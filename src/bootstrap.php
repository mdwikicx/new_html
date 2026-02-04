<?php

declare(strict_types=1);

define("DEBUGX", true);

if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$GLOBALS['MDWIKI_NEW_HTML_REVISIONS_DIR'] = dirname(__DIR__) . '/revisions_new';

$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    throw new RuntimeException('Composer autoloader not found. Run "composer install" first.');
}

require_once $autoloadPath;

require_once __DIR__ . '/Infrastructure/Utils/FileUtils.php';
require_once __DIR__ . '/Infrastructure/Storage/JsonStorage.php';
require_once __DIR__ . '/Services/Api/HttpClient.php';
require_once __DIR__ . '/Domain/Fixes/Structure/FixLanguageLinksFixture.php';
