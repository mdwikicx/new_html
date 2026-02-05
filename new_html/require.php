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

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Handle the case where the autoload file does not exist
    error_log('Autoload file not found');
    echo('vendor/autoload.php not found. Please run composer install to set up dependencies.');
    throw new RuntimeException('Autoload file not found');
}
/*
    include_once __DIR__ . '/Infrastructure/Debug/PrintHelper.php';

    require_once __DIR__ . "/Domain/Parser/CategoryParser.php";
    require_once __DIR__ . "/Domain/Parser/CitationsParser.php";
    require_once __DIR__ . "/Domain/Parser/ParserTemplate.php";
    require_once __DIR__ . "/Domain/Parser/ParserTemplates.php";
    require_once __DIR__ . "/Domain/Parser/Template.php";
    require_once __DIR__ . "/Domain/Parser/LeadSectionParser.php";

    require_once __DIR__ . "/Domain/Fixes/Media/FixImagesFixture.php";
    require_once __DIR__ . "/Domain/Fixes/Media/RemoveMissingImagesFixture.php";
    require_once __DIR__ . "/Domain/Fixes/References/DeleteEmptyRefsFixture.php";
    require_once __DIR__ . "/Domain/Fixes/References/ExpandRefsFixture.php";
    require_once __DIR__ . "/Domain/Fixes/References/RefWorkerFixture.php";
    require_once __DIR__ . "/Domain/Fixes/Structure/FixCategoriesFixture.php";
    require_once __DIR__ . "/Domain/Fixes/Structure/FixLanguageLinksFixture.php";
    require_once __DIR__ . "/Domain/Fixes/Templates/DeleteTemplatesFixture.php";
    require_once __DIR__ . "/Domain/Fixes/Templates/FixTemplatesFixture.php";

    require_once __DIR__ . "/Services/Api/HttpClient.php";
    require_once __DIR__ . "/Services/Api/MdwikiApiService.php";
    require_once __DIR__ . "/Services/Api/TransformApiService.php";
    require_once __DIR__ . "/Services/Api/SegmentApiService.php";
    require_once __DIR__ . "/Services/Api/CommonsApiService.php";
    require_once __DIR__ . "/Services/Wikitext/WikitextFixerService.php";

    require_once __DIR__ . "/Infrastructure/Utils/HtmlUtils.php";
    require_once __DIR__ . "/Infrastructure/Utils/FileUtils.php";

    require_once __DIR__ . "/Services/Html/HtmlToSegmentsService.php";
    require_once __DIR__ . "/Services/Html/WikitextToHtmlService.php";

    require_once __DIR__ . "/Application/Handlers/WikitextHandler.php";
    require_once __DIR__ . "/Application/Controllers/JsonDataController.php";
*/
