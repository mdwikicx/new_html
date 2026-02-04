<?php

if (defined('DEBUGX') && DEBUGX) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

include_once __DIR__ . '/new_html_src/require.php';
require_once __DIR__ . "/json_data.php";
