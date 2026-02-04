<?php

namespace Segments;
/*
use function Segments\html_to_seg;
*/

use function NewHtml\FileHelps\file_write;
use function NewHtml\FileHelps\read_file;
// use function APIServices\post_url_params_result;
use function APIServices\change_html_to_seg;

function do_html_to_seg($text): string
{

    $fixed = change_html_to_seg($text);

    // $error  = $fixed['error'] ?? '';
    $result = $fixed['result'] ?? "";

    // $result = str_replace("https://medwiki.toolforge.org/md/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/w/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/wiki/", "https://en.wikipedia.org/wiki/", $result);

    if ($result == 'Content for translate is not given or is empty') return "";

    return $result;
}

function html_to_seg($text, $file_seg): array
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
