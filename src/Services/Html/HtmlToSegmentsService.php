<?php

namespace MDWiki\NewHtml\Services\Html;

use function MDWiki\NewHtml\Services\Api\change_html_to_seg;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;

function do_html_to_seg(string $text): string
{

    $fixed = change_html_to_seg($text);

    $result = $fixed['result'] ?? "";

    if ($result == 'Content for translate is not given or is empty') return "";
    return $result;
}

function html_to_seg(string $text, string $file_seg): array
{

    $from_cache = false;

    if (!isset($_GET['new'])) {
        $seg_text = read_file($file_seg);

        if ($seg_text != '') {
            return [$seg_text, true];
        }
    }

    $result = do_html_to_seg($text);

    if ($result == '') return ["", $from_cache];

    file_write($file_seg, $result);

    return [$result, $from_cache];
}
