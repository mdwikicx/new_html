<?php

namespace FixRefs\Tests;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$vendor_path = __DIR__ . '/../vendor/autoload.php';

if (file_exists($vendor_path)) {
    require $vendor_path;
};

require __DIR__ . '/../new_html_src/require.php';

use PHPUnit\Framework\TestCase;

class MyFunctionTest extends TestCase {}
