<?php

namespace Printn;
/*
use function Printn\test_print;
*/

function test_print($str)
{
    if (isset($_GET['test']) || defined('DEBUGX')) {
        echo $str;
        echo "\n";
    }
}
