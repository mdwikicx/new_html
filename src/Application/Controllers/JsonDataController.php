<?php

namespace MDWiki\NewHtml\Application\Controllers;

use function MDWiki\NewHtml\Infrastructure\Storage\dump_both_data;
use function MDWiki\NewHtml\Infrastructure\Storage\get_Data;
use function MDWiki\NewHtml\Infrastructure\Storage\get_from_json;
use function MDWiki\NewHtml\Infrastructure\Storage\get_title_revision;
use function MDWiki\NewHtml\Infrastructure\Storage\add_title_revision;

class JsonDataController
{
    public function getTitleRevision(string $title, string $all = ''): string
    {
        return get_title_revision($title, $all);
    }

    public function addTitleRevision(string $title, string $revision, string $all = ''): array|string
    {
        return add_title_revision($title, $revision, $all);
    }

    public function getFromJson(string $title, string $all = ''): array
    {
        return get_from_json($title, $all);
    }

    public function getData(string $type = ''): array
    {
        return get_Data($type);
    }

    public function dumpBothData(array $mainData, array $allData): void
    {
        dump_both_data($mainData, $allData);
    }
}
