<?php

namespace MDWiki\NewHtml\Application\Handlers;

use function MDWiki\NewHtml\Services\Wikitext\process_wikitext;
use function MDWiki\NewHtml\Infrastructure\Storage\get_from_json;

class WikitextHandler
{
    public function handle(string $title, string $all = ''): array
    {
        return get_from_json($title, $all);
    }

    public function fetchAndProcess(string $title, string $all = ''): array
    {
        return process_wikitext('', $title, $all);
    }
}
