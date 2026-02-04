<?php

namespace Fixes\FixImages;

/*
Usage:

use function Fixes\FixImages\remove_images;
use function Fixes\FixImages\remove_videos;

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $text, string $start)
    {
        return strpos($text, $start) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $string, string $endString)
    {
        $len = strlen($endString);
        return substr($string, -$len) === $endString;
    }
}

*/

function remove_images($text): string
{
    $pattern = '/\[\[(File:[^\]\[\|]+)\|([^\]\[]*(\[\[[^\]\[]+\]\][^\]\[]*)*)\]\]/x';

    preg_match_all($pattern, $text, $matches);

    $images = [];
    // array ( 'File:AwareLogo.png' => '[[File:AwareLogo.png|thumb|upright=1.3|Logo of the WHO Aware Classification]]', )

    foreach ($matches[0] as $link) {
        $file_name = $matches[1][array_search($link, $matches[0])];

        $new_text = sprintf("{{subst:#ifexist:%s|%s}}", $file_name, $link);

        $text = str_replace($link, $new_text, $text);

        $images[$file_name] = $link;

    }

    // echo "<pre>";
    // echo htmlentities(var_export($images, true));
    // echo "</pre><br>";

    return $text;
}

/*

remove texts like:
- [[File:Schizophrenia video.webm|frameless|upright=1.36|Video explanation by Osmosis]]
- [[File:En.Wikipedia-VideoWiki-Schizophrenia.webm|thumb|thumbtime=2:25|upright=1.36|Video summary ([[Video:Schizophrenia|script]])]]

*/
function remove_videos($text): string
{
    $pattern = '/\[\[(File:[^\]\[\|]+)\|([^\]\[]*(\[\[[^\]\[]+\]\][^\]\[]*)*)\]\]/x';

    $video_exts = ['webm', 'ogv', 'ogg', 'mp4'];

    preg_match_all($pattern, $text, $matches);

    foreach ($matches[0] as $link) {

        // file_name example: File:AwareLogo.webm

        $file_name = $matches[1][array_search($link, $matches[0])];
        $ext = strtolower((string) pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $video_exts, true)) {
            $text = str_replace($link, '', $text);
        }
    }

    return $text;
}
