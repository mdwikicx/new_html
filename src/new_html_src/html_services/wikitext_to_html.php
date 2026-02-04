<?php

namespace Html;
/*
use function Html\wiki_text_to_html;
*/

use function HtmlFixes\fix_link_red;
use function HtmlFixes\del_div_error;
use function NewHtml\FileHelps\file_write; // file_write($file_html, $result);
use function NewHtml\FileHelps\read_file;
use function APIServices\convert_wikitext_to_html;

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
