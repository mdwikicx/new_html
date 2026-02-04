<?php

include_once __DIR__ . '/print.php';

require_once __DIR__ . "/WikiParse/Category.php";
require_once __DIR__ . "/WikiParse/Citations_reg.php";
require_once __DIR__ . "/WikiParse/ParserTemplates.php";
require_once __DIR__ . "/WikiParse/Template.php";

foreach (glob(__DIR__ . "/WikiTextFixes/*.php") as $filename) {
    include_once $filename;
}

require_once __DIR__ . "/fix_wikitext.php";

require_once __DIR__ . "/file_helps.php";
require_once __DIR__ . "/post.php";
require_once __DIR__ . "/post_mdwiki.php";
require_once __DIR__ . "/fix_html.php";

require_once __DIR__ . "/services/html_to_Segments.php";
require_once __DIR__ . "/services/wikitext_to_html.php";

require_once __DIR__ . "/WikiText/lead_section.php";
require_once __DIR__ . "/WikiText/index.php";
