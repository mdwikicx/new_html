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
$src_path = __DIR__ . '/src/';

if (!is_dir($src_path)) {
    $src_path = __DIR__ . '/../src/';
}

require_once __DIR__ . "/utils.php";
require_once $src_path . "/bootstrap.php";
require_once $src_path . "/Application/Controllers/JsonDataController.php";
require_once $src_path . "/Application/Handlers/WikitextHandler.php";
require_once $src_path . "/Domain/Fixes/Media/FixImagesFixture.php";
require_once $src_path . "/Domain/Fixes/Media/RemoveMissingImagesFixture.php";
require_once $src_path . "/Domain/Fixes/References/DeleteEmptyRefsFixture.php";
require_once $src_path . "/Domain/Fixes/References/ExpandRefsFixture.php";
require_once $src_path . "/Domain/Fixes/References/RefWorkerFixture.php";
require_once $src_path . "/Domain/Fixes/Structure/FixCategoriesFixture.php";
require_once $src_path . "/Domain/Fixes/Structure/FixLanguageLinksFixture.php";
require_once $src_path . "/Domain/Fixes/Templates/DeleteTemplatesFixture.php";
require_once $src_path . "/Domain/Fixes/Templates/FixTemplatesFixture.php";
require_once $src_path . "/Domain/Parser/CategoryParser.php";
require_once $src_path . "/Domain/Parser/CitationsParser.php";
require_once $src_path . "/Domain/Parser/LeadSectionParser.php";
require_once $src_path . "/Domain/Parser/ParserTemplate.php";
require_once $src_path . "/Domain/Parser/ParserTemplates.php";
require_once $src_path . "/Domain/Parser/Template.php";
require_once $src_path . "/Infrastructure/Debug/PrintHelper.php";
require_once $src_path . "/Infrastructure/Utils/FileUtils.php";
require_once $src_path . "/Infrastructure/Utils/HtmlUtils.php";
require_once $src_path . "/Services/Api/CommonsApiService.php";
require_once $src_path . "/Services/Api/HttpClient.php";
require_once $src_path . "/Services/Api/MdwikiApiService.php";
require_once $src_path . "/Services/Api/SegmentApiService.php";
require_once $src_path . "/Services/Api/TransformApiService.php";
require_once $src_path . "/Services/Html/HtmlToSegmentsService.php";
require_once $src_path . "/Services/Html/WikitextToHtmlService.php";
require_once $src_path . "/Services/Wikitext/WikitextFixerService.php";
