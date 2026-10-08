<?php

namespace Tests;

// Set test environment
putenv('APP_ENV=testing');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$env_value = getenv('REVISIONS_DIR') ?: ($_ENV['REVISIONS_DIR'] ?? null);
if (! $env_value) {
    putenv(
        'REVISIONS_DIR='
        . rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR
        . 'mdwiki-newhtml-revisions'
    );
}

// Check environment variable to enable network tests
// Usage: RUN_NETWORK_TESTS=true vendor/bin/phpunit tests/NetworkRealTests
$runNetworkTests = getenv('RUN_NETWORK_TESTS') === 'true';

// Define constant for use in test classes
define('RUN_NETWORK_TESTS', $runNetworkTests);

// Use modern PSR-4 autoloading via bootstrap
require __DIR__ . '/../src/bootstrap.php';
