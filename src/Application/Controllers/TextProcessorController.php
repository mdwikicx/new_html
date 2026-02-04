<?php

namespace MDWiki\NewHtml\Application\Controllers;

use MDWiki\NewHtml\Application\Handlers\WikitextHandler;
use function MDWiki\NewHtml\Services\Html\wiki_text_to_html;
use function MDWiki\NewHtml\Services\Html\html_to_seg;
use MDWiki\NewHtml\Services\Html\wiki_text_to_html;
use MDWiki\NewHtml\Services\Html\html_to_seg;
use function MDWiki\NewHtml\Infrastructure\Storage\get_from_json;
use function MDWiki\NewHtml\Infrastructure\Storage\add_title_revision;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\get_file_dir;

class TextProcessorController
{
    private WikitextHandler $wikitextHandler;

    public function __construct()
    {
        $this->wikitextHandler = new WikitextHandler();
    }

    public function processPage(string $title, string $all = ''): array
    {
        $result = $this->wikitextHandler->handle($title, $all);
        return $result;
    }

    public function convertToHtml(string $wikitext, string $title, string $fileHtml, bool $new = false): array
    {
        return wiki_text_to_html($wikitext, $fileHtml, $title, $new);
    }

    public function convertToSegments(string $html, string $fileSeg): array
    {
        return html_to_seg($html, $fileSeg);
    }

    public function saveWikitext(string $revid, string $wikitext, string $all = ''): void
    {
        $fileDir = get_file_dir($revid, $all);
        file_write($fileDir . '/wikitext.txt', $wikitext);
    }
}
