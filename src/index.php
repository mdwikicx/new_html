<?php

use MDWiki\NewHtml\Controllers\AppRouterController;

include_once __DIR__ . "/bootstrap.php";

if ((empty($_GET) && empty($_POST)) || (count($_GET) == 1 && isset($_GET["test"]))) {
    // require_once __DIR__ . "/revisions.html";
    header("Location: revisions.html");
} else {
    $controller = new AppRouterController();
    $controller->handleRequest($_GET);
}
