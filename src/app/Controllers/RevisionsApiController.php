<?php
namespace MDWiki\NewHtml\Controllers;

/**
 * Reisions API
 *
 * Returns JSON data for revisions dashboard.
 *
 * @package MDWiki\NewHtml
 */

use MDWiki\NewHtml\Infrastructure\Utils\FileUtils;
use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Settings;

class RevisionsApiController
{
    private Settings $settings;
    public string $json_data;
    public string $json_data_all;

    public function __construct()
    {
        $this->settings      = Settings::getInstance();
        $this->json_data_all = $this->settings->jsonFileAll;
        $this->json_data     = $this->settings->jsonFile;
    }

    /**
     * Get data from JSON file based on type
     *
     * @param string $tyt The type of data to retrieve ('all' for complete data, otherwise main data)
     * @return array<string, mixed> The decoded JSON data as an array
     */
    public function getData(string $tyt): array
    {
        $file      = ($tyt == 'all') ? $this->json_data_all : $this->json_data;
        $file_text = FileUtils::read_file($file);
        if (empty($file_text)) {
            return [];
        }

        $data = json_decode($file_text, true) ?? [];
        return $data;
    }

    public function fileWrite(?string $file, string $text): void
    {
        if (empty($text) || empty($file)) {
            return;
        }

        try {
            file_put_contents($file, $text, LOCK_EX);
        } catch (\Exception $e) {
            Logger::error("Error: Could not write to file: $file");
        }
    }
    private function setHeader(): void
    {
        header("Content-type: application/json; charset=utf-8");
    }

    public function handleRequest(): void
    {
        $this->setHeader();

        $dirs = array_filter(glob($this->settings->RevisionsDirPath . '/*/'), 'is_dir');

        // sort directories by last modified date
        usort($dirs, function ($a, $b) {
            $timeA = is_file($a . '/wikitext.txt') ? filemtime($a . '/wikitext.txt') : filemtime($a);
            $timeB = is_file($b . '/wikitext.txt') ? filemtime($b . '/wikitext.txt') : filemtime($b);
            return $timeB - $timeA;
        });

        $results       = [];
        $number        = 0;
        $main_data     = $this->getData('');
        $main_data_all = $this->getData('all');

        $make_dump = empty($main_data);

        foreach ($dirs as $dir) {
            $number += 1;

            $wikitextFile = $dir . '/wikitext.txt';
            $lastModified = is_file($wikitextFile)
                ? date('Y-m-d H:i', filemtime($wikitextFile))
                : date('Y-m-d H:i', filemtime($dir));

            $dir          = rtrim($dir, '/');
            $dir_path     = basename($dir);
            $oldid_number = str_replace('_all', '', $dir_path);

            $files = array_filter(glob("$dir/*"), 'is_file');
            $files = array_map('basename', $files);

            $wikitext_exists = in_array('wikitext.txt', $files);
            $html_exists     = in_array('html.html', $files);
            $seg_exists      = in_array('seg.html', $files);

            $title_path = "$dir/title.txt";
            $title      = (is_file($title_path)) ? file_get_contents($title_path) : '';
            $title      = str_replace('_', ' ', $title);

            if (! empty($title) && $make_dump && ! empty($oldid_number)) {
                // @phpstan-ignore nullCoalesce.expr
                $id = (int) $oldid_number ?? 0;
                if ($id > 0) {
                    if (strpos($dir_path, '_all') !== false) {
                        $main_data_all[$title] = $id;
                    } else {
                        $main_data[$title] = $id;
                    }
                }
            }

            $results[] = [
                'number'          => $number,
                'lastModified'    => $lastModified,
                'title'           => $title,
                'dir_path'        => $dir_path,
                'oldid_number'    => $oldid_number,
                'wikitext_exists' => $wikitext_exists,
                'html_exists'     => $html_exists,
                'seg_exists'      => $seg_exists,
            ];
        }

        if ($make_dump) {
            $this->fileWrite($this->json_data, json_encode($main_data, JSON_PRETTY_PRINT));
            $this->fileWrite($this->json_data_all, json_encode($main_data_all, JSON_PRETTY_PRINT));
        }

        $this->respond(['results' => $results]);
    }
    // ------------------------------------------------------------
    // Response helpers
    // ------------------------------------------------------------

    private function respond(array $data): void
    {
        print(json_encode($data));
    }
}
