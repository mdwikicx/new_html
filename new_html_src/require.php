<?php

include_once __DIR__ . '/print.php';

require_once __DIR__ . "/WikiParse/Category.php";
require_once __DIR__ . "/WikiParse/Citations_reg.php";
require_once __DIR__ . "/WikiParse/ParserTemplates.php";
require_once __DIR__ . "/WikiParse/Template.php";
require_once __DIR__ . "/WikiParse/lead_section.php";

foreach (glob(__DIR__ . "/WikiTextFixes/*.php") as $filename) {
    include_once $filename;
}

require_once __DIR__ . "/api_services/post.php";
require_once __DIR__ . "/api_services/mdwiki_api_wikitext.php";
require_once __DIR__ . "/api_services/seg_api.php";
require_once __DIR__ . "/api_services/transform_api.php";

require_once __DIR__ . "/fix_wikitext.php";

require_once __DIR__ . "/utils/html_utils.php";
require_once __DIR__ . "/utils/files_utils.php";
require_once __DIR__ . "/post_mdwiki.php";

require_once __DIR__ . "/html_services/html_to_segments.php";
require_once __DIR__ . "/html_services/wikitext_to_html.php";

require_once __DIR__ . "/get_text.php";
