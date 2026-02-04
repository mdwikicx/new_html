<?php

namespace MDWiki\NewHtml\Services\Html;

use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;
use function MDWiki\NewHtml\Services\Api\convert_wikitext_to_html;
use function MDWiki\NewHtml\Infrastructure\Utils\fix_link_red;
use function MDWiki\NewHtml\Infrastructure\Utils\del_div_error;

function do_wiki_text_to_html(string $wikitext, string $title): mixed
{

    $title = str_replace(" ", "_", $title);

    if ($wikitext == '') return "";

    $fixed = convert_wikitext_to_html($wikitext, $title);

    $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? '';

    if ($result == '') return "";

    $result = del_div_error($result);
    $result = fix_link_red($result);
    return $result;
}

function wiki_text_to_html(string $wikitext, string $file_html, string $title, bool $new): array
{

    $from_cache = false;

    if (!$new) {

        $text = read_file($file_html);

        if ($text != '') return [$text, true];
    }

    if ($wikitext == '') return ["", $from_cache];

    $result = do_wiki_text_to_html($wikitext, $title);

    if ($result == '') return ["", $from_cache];

    file_write($file_html, $result);

    return [$result, $from_cache];
}
