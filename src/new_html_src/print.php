<?php

namespace Printn;
/*
use function Printn\test_print;
*/

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
