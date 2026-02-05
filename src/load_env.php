<?php

use Dotenv\Dotenv;

$home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');

$homeEnv = $home . '/.env';
try {
    if (isset($home) && file_exists($homeEnv)) {
        Dotenv::createImmutable($home)->load();
    } else {
        Dotenv::createImmutable(dirname(__DIR__))->load();
    }
} catch (Exception $e) {
    // Handle exception if needed
    error_log('Failed to load environment variables: ' . $e->getMessage());
}
