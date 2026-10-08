<?php

/**
 * Bootstrap file for MDWiki NewHtml application
 *
 * This file initializes the application environment, loads the Composer
 * autoloader, and sets up necessary configuration constants.
 *
 * @package MDWiki\NewHtml
 */

require_once __DIR__ . "/autoload.php";
require_once __DIR__ . "/Controllers/main.php";

require_once __DIR__ . "/Handlers/WikitextHandler.php";
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
