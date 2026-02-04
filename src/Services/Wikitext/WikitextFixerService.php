<?php

namespace MDWiki\NewHtml\Services\Wikitext;

use function MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs;
use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_categories;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_videos;
use function MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\remove_lead_templates;
use function MDWiki\NewHtml\Domain\Fixes\Templates\add_missing_title;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_missing_images;
use function MDWiki\NewHtml\Infrastructure\Parser\get_lead_section;
use function MDWiki\NewHtml\Domain\Fixes\References\refs_expend_work;

function fix_wikitext(string $text, string $title): string
{
    $text = str_replace("{{drugbox", "{{Infobox drug", $text);
    $text = str_replace("{{Drugbox", "{{Infobox drug", $text);

    $text = remove_templates($text);
    $text = remove_lead_templates($text);

    $text = remove_bad_refs($text);
    $text = del_empty_refs($text);

    $text = remove_videos($text);

    $text = remove_categories($text);

    $text = remove_missing_images($text);

    $text = add_missing_title($text, $title);

    return $text;
}

function process_wikitext(string $text, string $title, string $all): array
{
    $text = str_replace(" ", "_", $title);

    $json1 = get_wikitext_from_mdwiki_restapi($title);

    $source = $json1[0];
    $revid = $json1[1];

    if (preg_match('/#REDIRECT \[\[(.*?)\]\]/i', $source, $matches)) {
        $title = $matches[1];
        $json1 = get_wikitext_from_mdwiki_restapi($title);
        $source = $json1[0];
        $revid = $json1[1];
    }

    if ($source != '') {
        if ($all == '') {
            $full_text = $source;
            $lead = get_lead_section($source);
            if ($lead != '') {
                $source = refs_expend_work($lead, $full_text);
            }
        }

        $source = fix_wikitext($source, $title);
    }

    if (empty($source)) {
    };

    if (!empty($revid)) {
        add_title_revision($title, $revid, $all);
    }

    return [$source, $revid];
}
