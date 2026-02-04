<?php

foreach (glob(__DIR__ . "/src/DataModel/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/src/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__) {
        continue;
    }
    include_once $filename;
}
