<?php

use MDWiki\NewHtml\Controllers\RevisionsApiController;

require_once __DIR__ . "/bootstrap.php";

$controller = new RevisionsApiController();
$controller->handleRequest();
