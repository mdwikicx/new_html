<?php

/**
 * Debug printing utilities
 *
 * Provides functions for conditional debug output based on
 * request parameters or DEBUGX constant.
 *
 * @package MDWiki\NewHtml\Infrastructure\Debug
 */

namespace MDWiki\NewHtml\Infrastructure\Debug;

/**
 * Print debug messages when test mode is enabled
 *
 * @param mixed $str The string or value to print
 * @return void
 */
function test_print(mixed $str): void
{
    if (isset($_GET['test']) || defined('DEBUGX')) {
        echo $str;
        echo "\n";
    }
}
