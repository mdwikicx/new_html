<?php

namespace MDWiki\NewHtml\Domain\Parser;

function get_ref_name(string $options): string
{
    if (empty(trim($options))) {
        return "";
    }
    $pa = "/name\s*\=\s*[\"\']*([^>\"\']*)[\"\']*\s*/i";
    preg_match($pa, $options, $matches);

    if (!isset($matches[1])) {
        return "";
    }
    $name = trim($matches[1]);
    return $name;
}

function get_regex_citations(string $text): array
{
    preg_match_all("/<ref([^\/>]*?)>(.+?)<\/ref>/is", $text, $matches);

    $citations = [];

    foreach ($matches[1] as $key => $citation_options) {
        $content = $matches[2][$key];
        $ref_tag = $matches[0][$key];
        $options = $citation_options;
        $citation = [
            "content" => $content,
            "tag" => $ref_tag,
            "name" => get_ref_name($options),
            "options" => $options
        ];
        $citations[] = $citation;
    }
    return $citations;
}

function get_full_refs(string $text): array
{
    $full = [];
    $citations = get_regex_citations($text);

    foreach ($citations as $cite) {
        $name = $cite["name"];
        $ref = $cite["tag"];

        if (empty($name)) {
            continue;
        }

        $full[$name] = $ref;
    };
    return $full;
}

function get_short_citations(string $text): array
{
    preg_match_all("/<ref ([^\/>]*?)\/\s*>/is", $text, $matches);

    $citations = [];

    foreach ($matches[1] as $key => $citation_options) {
        $ref_tag = $matches[0][$key];
        $options = $citation_options;
        $citation = [
            "content" => "",
            "tag" => $ref_tag,
            "name" => get_ref_name($options),
            "options" => $options
        ];
        $citations[] = $citation;
    }

    return $citations;
}
