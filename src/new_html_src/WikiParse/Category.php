<?php

namespace WikiParse\Category;

/*
Usage:

use function WikiParse\Category\get_categories;

*/

function get_categories(string $text): array
{
    // $parser = new ParserCategories($text);
    // $categories = $parser->getCategories();

    $categories = [];

    preg_match_all("/\[\[\s*Category\s*\:([^\]\]]+?)\]\]/is", $text, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[0] as $u => $ca) {
            $mvalue = $matches[1][$u];
            $bleh = explode("|", $mvalue);
            $category = trim(array_shift($bleh));
            $bleh = null;
            $categories[$category] = $ca;
            // echo $ca . "<br>";
        }
    };
    return $categories;
}
