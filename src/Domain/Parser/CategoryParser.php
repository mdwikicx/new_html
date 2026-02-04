<?php

namespace MDWiki\NewHtml\Domain\Parser;

function get_categories(string $text): array
{
    $categories = [];

    preg_match_all("/\[\[\s*Category\s*\:([^\]\]]+?)\]\]/is", $text, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[0] as $u => $ca) {
            $mvalue = $matches[1][$u];
            $bleh = explode("|", $mvalue);
            $category = trim(array_shift($bleh));
            $bleh = null;
            $categories[$category] = $ca;
        }
    };
    return $categories;
}
