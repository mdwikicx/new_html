<?php

use Dotenv\Dotenv;

$home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');

$homeEnv = $home . '/.env';

if (isset($home) && file_exists($homeEnv)) {
    Dotenv::createImmutable($home)->load();
} else {
    Dotenv::createImmutable(APP_ROOT)->load();
}
