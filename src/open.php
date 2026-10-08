<?php

use MDWiki\NewHtml\Controllers\OpenController;

require_once __DIR__ . "/bootstrap.php";

$controller = new OpenController();
$controller->handleRequest($_GET);
