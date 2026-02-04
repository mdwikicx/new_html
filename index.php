<?php

if ((empty($_GET) && empty($_POST)) || (count($_GET) == 1 && isset($_GET["test"]))) {
    // require_once __DIR__ . "/revisions_new.php";
    header("Location: revisions_new.php");
} else {
    require_once __DIR__ . "/main.php";
}
