<?php

use Dotenv\Dotenv;

$home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');

$homeEnv = $home . '/.env';
try {
    if (!empty($home) && file_exists($homeEnv)) {
        $dotenv = Dotenv::createImmutable($home);
    } else {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    }
    $dotenv->load();
} catch (Exception $e) {
    // Handle exception if needed
    error_log('Failed to load environment variables: ' . $e->getMessage());
    // echo 'Failed to load environment variables. Please check the logs for details.';
}
