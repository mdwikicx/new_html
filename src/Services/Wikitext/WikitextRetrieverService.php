<?php

namespace MDWiki\NewHtml\Services\Wikitext;

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;
use function MDWiki\NewHtml\Services\Api\get_wikitext_from_mdwiki_restapi;
use function MDWiki\NewHtml\Infrastructure\Storage\add_title_revision;

function get_wikitext(string $title, string $all): array
{

    $title = str_replace(" ", "_", $title);

    $json1 = get_wikitext_from_mdwiki_restapi($title);

    $source = $json1[0];
    $revid = $json1[1];

    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        test_print("Redirecting to: $title\n");
        $json1 = get_wikitext_from_mdwiki_restapi($title);
        $source = $json1[0];
        $revid = $json1[1];
    }

    if (empty($revid)) {
        test_print("wikitext empty!.");
    };

    if (!empty($revid)) {
        add_title_revision($title, $revid, $all);
    }

    return [$source, $revid];
}
