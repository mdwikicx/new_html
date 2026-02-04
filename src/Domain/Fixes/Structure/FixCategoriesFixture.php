<?php

namespace MDWiki\NewHtml\Domain\Fixes\Structure;

use function MDWiki\NewHtml\Domain\Parser\get_categories;

function remove_categories(string $text): string
{

    $categories = get_categories($text);

    foreach ($categories as $name => $cat) {
        $text = str_replace($cat, '', $text);
    }

    return $text;
}
