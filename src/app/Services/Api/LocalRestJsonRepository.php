<?php
namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Logger;

/**
 * Reads cached MDWiki REST responses from the local data directory.
 */
class LocalRestJsonRepository
{
    private string $baseDir;

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? self::defaultDir();
    }

    public static function titleToFileName(string $title): string
    {
        return MdwikiApiService::encodeTitle($title) . '.json';
    }

    private static function defaultDir(): string
    {
        $home = getenv('HOME');
        if ($home === false || $home === '') {
            $info = function_exists('posix_getpwuid') ? posix_getpwuid(posix_getuid()) : false;
            $home = $info['dir'] ?? '';
        }
        return rtrim($home, '/') . '/data/rtt_members';
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

        $file = realpath($dir . '/' . self::titleToFileName($title));
        if ($file === false || strpos($file, $dir . DIRECTORY_SEPARATOR) !== 0 || ! is_file($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($json)) {
            Logger::error("LocalRestJsonRepository: invalid json in local file for title: $title");
            return null;
        }

        return $json;
    }
}
