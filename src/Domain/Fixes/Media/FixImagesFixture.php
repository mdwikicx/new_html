<?php

namespace MDWiki\NewHtml\Domain\Fixes\Media;

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

function remove_images(string $text): string
{
    $pattern = '/\[\[(File:[^\]\[\|]+)\|([^\]\[]*(\[\[[^\]\[]+\]\][^\]\[]*)*)\]\]/x';

    preg_match_all($pattern, $text, $matches);

    $images = [];

    foreach ($matches[0] as $link) {
        $file_name = $matches[1][array_search($link, $matches[0])];

        $new_text = sprintf("{{subst:#ifexist:%s|%s}}", $file_name, $link);

        $text = str_replace($link, $new_text, $text);

        $images[$file_name] = $link;
    }

    return $text;
}

function remove_videos(string $text): string
{
    $pattern = '/\[\[(File:[^\]\[\|]+)\|([^\]\[]*(\[\[[^\]\[]+\]\][^\]\[]*)*)\]\]/x';

    $video_exts = ['webm', 'ogv', 'ogg', 'mp4'];

    preg_match_all($pattern, $text, $matches);

    foreach ($matches[0] as $link) {

        $file_name = $matches[1][array_search($link, $matches[0])];
        $ext = strtolower((string) pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $video_exts, true)) {
            $text = str_replace($link, '', $text);
        }
    }

    return $text;
}
