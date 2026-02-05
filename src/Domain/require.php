<?php

/**
 * Source file loader for Domain module
 *
 * This file loads all necessary source files for the Domain module,
 * including parsing utilities, API services, text fixes, HTML services,
 * and helper utilities. It uses a mix of require_once for core files
 * and glob patterns for extensibility.
 *
 * @package MDWiki\NewHtml
 */

include_once __DIR__ . '/../Infrastructure/Debug/PrintHelper.php';

require_once __DIR__ . "/WikiParse/Category.php";
require_once __DIR__ . "/WikiParse/Citations_reg.php";

require_once __DIR__ . "/../Domain/Parser/ParserTemplate.php";
require_once __DIR__ . "/../Domain/Parser/ParserTemplates.php";
require_once __DIR__ . "/../Domain/Parser/Template.php";

require_once __DIR__ . "/WikiParse/lead_section.php";

foreach (glob(__DIR__ . "/Fixes/*.php") as $filename) {
    include_once $filename;
}

require_once __DIR__ . "/../Services/Api/HttpClient.php";
require_once __DIR__ . "/../Services/Api/MdwikiApiService.php";
require_once __DIR__ . "/../Services/Api/TransformApiService.php";
require_once __DIR__ . "/../Services/Api/SegmentApiService.php";
require_once __DIR__ . "/../Services/Api/CommonsApiService.php";
require_once __DIR__ . "/../Services/Wikitext/WikitextFixerService.php";

require_once __DIR__ . "/../Infrastructure/Utils/HtmlUtils.php";
require_once __DIR__ . "/../Infrastructure/Utils/FileUtils.php";

require_once __DIR__ . "/../Services/Html/HtmlToSegmentsService.php";
require_once __DIR__ . "/../Services/Html/WikitextToHtmlService.php";

require_once __DIR__ . "/../Application/Handlers/PostMdwikiHandler.php";
require_once __DIR__ . "/../Application/Handlers/WikitextHandler.php";
require_once __DIR__ . "/../Application/Controllers/JsonDataController.php";
