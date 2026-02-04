<?php

namespace MDWiki\NewHtml\Infrastructure\Debug;

function test_print(mixed $str): void
{
    if (isset($_GET['test']) || defined('DEBUGX')) {
        echo $str;
        echo "\n";
    }
}
