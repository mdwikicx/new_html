<?php

namespace Fixes\RefWork;

use function MDWiki\NewHtml\Domain\Fixes\References\check_one_cite as new_check_one_cite;
use function MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs as new_remove_bad_refs;

function check_one_cite(string $cite): bool
{
    return new_check_one_cite($cite);
}

function remove_bad_refs(string $text): string
{
    return new_remove_bad_refs($text);
}
