<?php

namespace Fixes\ExpendRefs;

/*
Usage:

use function Fixes\ExpendRefs\refs_expend_work;

*/

use function WikiParse\Reg_Citations\get_full_refs;
use function WikiParse\Reg_Citations\get_short_citations;
use function Printn\test_print;

function refs_expend_work(string $first, string $alltext): string
{
    if (empty($alltext)) {
        $alltext = $first;
    }

    test_print("refs_expend_work: \n");

    $allpage_fullrefs = get_full_refs($alltext);

    $lead_fullrefs = get_full_refs($first);
    $lead_short_refs = get_short_citations($first);
    // ---
    test_print(var_export($lead_short_refs, true));
    // ---
    foreach ($lead_short_refs as $cite) {
        // ---
        $name = $cite["name"] ?? '';
        $refe = $cite["tag"] ?? '';
        // ---
        if (empty($name) || empty($refe)) {
            continue;
        }
        // ---
        if (isset($lead_fullrefs[$name])) {
            continue;
        }
        // ---
        $rr = $allpage_fullrefs[$name] ?? "";
        // ---
        if (!empty($rr)) {
            test_print("refs_expend_work: name:($name), refe:($refe), rr:($rr)\n");
            $first = str_replace($refe, $rr, $first);
        }
    }
    // ---
    return $first;
}
