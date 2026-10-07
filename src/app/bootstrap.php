<?php

/**
 * Bootstrap file for MDWiki NewHtml application
 *
 * This file initializes the application environment, loads the Composer
 * autoloader, and sets up necessary configuration constants.
 *
 * @package MDWiki\NewHtml
 */

if (!defined('USER_AGENT')) {
    $user_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.mdwikicx@toolforge.org)';
    define('USER_AGENT', $user_agent);
}

require_once __DIR__ . "/autoload.php";
require_once __DIR__ . "/Utils.php";

require_once __DIR__ . "/Application/Controllers/JsonDataController.php";
require_once __DIR__ . "/Application/Handlers/WikitextHandler.php";
require_once __DIR__ . "/Domain/Fixes/Media/FixImagesFixture.php";

require_once __DIR__ . "/Domain/Fixes/References/DeleteEmptyRefsFixture.php";
require_once __DIR__ . "/Domain/Fixes/References/ExpandRefsFixture.php";
require_once __DIR__ . "/Domain/Fixes/References/RefWorkerFixture.php";
require_once __DIR__ . "/Domain/Fixes/Structure/FixCategoriesFixture.php";
require_once __DIR__ . "/Domain/Fixes/Structure/FixLanguageLinksFixture.php";
require_once __DIR__ . "/Domain/Fixes/Templates/DeleteTemplatesFixture.php";
require_once __DIR__ . "/Domain/Fixes/Templates/FixTemplatesFixture.php";

require_once __DIR__ . "/Domain/Parser/CategoryParser.php";
require_once __DIR__ . "/Domain/Parser/CitationsParser.php";
require_once __DIR__ . "/Domain/Parser/LeadSectionParser.php";

require_once __DIR__ . "/Infrastructure/Utils/FileUtils.php";
require_once __DIR__ . "/Infrastructure/Utils/HtmlUtils.php";

require_once __DIR__ . "/Services/Html/HtmlToSegmentsService.php";
