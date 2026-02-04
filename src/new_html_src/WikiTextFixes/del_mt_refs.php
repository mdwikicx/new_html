<?php
namespace Fixes\DelMtRefs;

/*
Usage:

use function Fixes\DelMtRefs\del_empty_refs;

*/


use function WikiParse\Reg_Citations\get_full_refs;
use function WikiParse\Reg_Citations\get_short_citations;

function del_empty_refs(string $first): string
{
    // ---
    $refs = get_full_refs($first);
    // echo  "refs:" . count($refs) . "<br>";

    $short_refs = get_short_citations($first);
    // echo  "short_refs:" . count($short_refs) . "<br>";

    foreach ($short_refs as $cite) {
        $name = $cite["name"];
        $refe = $cite["tag"];
        // ---
        $rr = $refs[$name] ?? false;
        if ($rr) {
            // if $rr already in $first : continue
            if (strpos($first, $rr) === false) {
                $first = str_replace($refe, $rr, $first);
            }
        } else {
            $first = str_replace($refe, "", $first);
        }
    }
    return $first;
}
