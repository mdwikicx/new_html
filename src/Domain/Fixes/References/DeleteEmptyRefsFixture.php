<?php

namespace MDWiki\NewHtml\Domain\Fixes\References;

use function MDWiki\NewHtml\Domain\Parser\get_full_refs;
use function MDWiki\NewHtml\Domain\Parser\get_short_citations;

function del_empty_refs(string $first): string
{

    $refs = get_full_refs($first);

    $short_refs = get_short_citations($first);

    foreach ($short_refs as $cite) {
        $name = $cite["name"];
        $refe = $cite["tag"];

        $rr = $refs[$name] ?? false;
        if ($rr) {
            if (strpos($first, $rr) === false) {
                $first = str_replace($refe, $rr, $first);
            }
        } else {
            $first = str_replace($refe, "", $first);
        }
    }
    return $first;
}
