<?php
namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Logger;

/**
 * Reads cached MDWiki REST responses from the local data directory.
 */
class LocalRestJsonRepository
{
    private const MAP_FILE_NAME = 'titles_map.json';

    private string $baseDir;

    /** Path to titles_map.json (by default, in the parent of the members dir) */
    private string $mapPath;

    /** @var array<string, string>|null Lazy-loaded title => file name map */
    private ?array $titlesMap = null;

    public function __construct(?string $baseDir = null, ?string $mapPath = null)
    {
        $this->baseDir = $baseDir ?? self::defaultDir();
        $this->mapPath = $mapPath ?? dirname(rtrim($this->baseDir, '/')) . '/' . self::MAP_FILE_NAME;
    }

    private static function defaultDir(): string
    {
        $home = getenv('HOME');
        if ($home === false || $home === '') {
            $info = function_exists('posix_getpwuid') ? posix_getpwuid(posix_getuid()) : false;
            $home = $info['dir'] ?? '';
        }
        return rtrim($home, '/') . '/data/rtt_members/members';
    }

    /**
     * Load the title => file name mapping from titles_map.json (cached).
     *
     * @return array<string, string>
     */
    private function loadTitlesMap(): array
    {
        if ($this->titlesMap !== null) {
            return $this->titlesMap;
        }

        $mapPath = $this->mapPath;
        $content = is_file($mapPath) ? file_get_contents($mapPath) : false;
        if ($content === false) {
            Logger::error("LocalRestJsonRepository: titles map not found: $mapPath");
            return []; // not cached, so a later call can retry
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            Logger::error("LocalRestJsonRepository: invalid titles map: $mapPath");
            return [];
        }

        $this->titlesMap = $data;
        return $this->titlesMap;
    }

    /**
     * Look up the JSON file name for a page title in the titles map.
     * Spaces and underscores are both accepted in the title.
     */
    public function titleToFileName(string $title): ?string
    {
        $map = $this->loadTitlesMap();

        return $map[$title] ?? $map[str_replace('_', ' ', $title)] ?? null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $title): ?array
    {
        $dir = realpath($this->baseDir);
        if ($dir === false) {
            return null;
        }

        $fileName = $this->titleToFileName($title);
        if ($fileName === null || $fileName === '') {
            return null;
        }

        $file = realpath($dir . '/' . $fileName);
        if ($file === false || strpos($file, $dir . DIRECTORY_SEPARATOR) !== 0 || !is_file($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            Logger::error("LocalRestJsonRepository: invalid json in local file for title: $title");
            return null;
        }

        return $json;
    }
}
