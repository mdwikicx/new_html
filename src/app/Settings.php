<?php
// src/app/Settings.php

declare (strict_types = 1);

namespace MDWiki\NewHtml;

/**
 * @property string $domain
 * @property string $userAgent
 * @property string $appEnv
 * @property string $RevisionsDirPath
 */
final class Settings
{
    // Private properties — access is controlled via __get()
    public string $domain;
    public string $ServerUrl;
    public string $userAgent;
    public string $appEnv;
    public string $RevisionsDirPath;
    public string $jsonFile;
    public string $jsonFileAll;

    private static ?self $instance = null;

    public function __construct()
    {
        $this->domain    = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $this->ServerUrl = $this->generateServerUrl();
        $this->userAgent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.mdwikicx@toolforge.org)';

        $this->appEnv           = $this->envVar('APP_ENV');
        $this->RevisionsDirPath = $this->envVar('REVISIONS_DIR');
        $this->jsonFile         = $this->RevisionsDirPath . '/json_data.json';
        $this->jsonFileAll      = $this->RevisionsDirPath . '/json_data_all.json';

        $this->init();
    }

    public static function getInstance(): self
    {
        // used: Settings::getInstance()
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    // Prevent cloning and unserialization of the singleton instance
    private function __clone()
    {}

    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }

    private function generateServerUrl(): string
    {
        /*
        "SERVER_PORT": "9001",
        "SERVER_NAME": "localhost",
        "HTTP_HOST": "localhost:9001",
        */
        // 1. Detect Protocol: Works for local development and production proxies
        $protocol = 'http';

        if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            // Standard SSL detection
            $protocol = 'https';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Detection for environments behind a Proxy/Load Balancer (e.g., Nginx, Cloudflare)
            $protocol = 'https';
        }

        // 2. Detect Host: HTTP_HOST captures both domain and port (e.g., localhost:9000)
        // This is OS-agnostic (Works the same on Windows and Linux)
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // 4. Build the final absolute URI
        return $protocol . "://" . $host;
    }

    public function isDevelopment(): bool
    {
        return $this->appEnv === "development";
    }

    public function isProduction(): bool
    {
        return $this->appEnv === "production";
    }

    public function isTesting(): bool
    {
        return $this->appEnv === "testing";
    }

    /**
     * Generates a dynamic Callback URL that works seamlessly on Windows (localhost)
     * and Linux (production) environments.
     * @param string $path The destination path (e.g., 'auth/callback')
     * @return string The absolute URL including protocol and host
     */
    public function generateCallbackUrl(string $path = '/auth/callback.php'): string
    {
        // Normalize Path: Ensure the path starts with a single forward slash
        $path = '/' . ltrim($path, '/');

        // Build the final absolute URI
        return $this->ServerUrl . $path;
    }

    private function envVar(string $key): string
    {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        return "";
    }
    public function OpenRevisionsDirPathFile(string $filePath): array
    {
        // remove right / from filePath
        $filePath = rtrim($filePath, '/');
        $path     = "{$this->RevisionsDirPath}/$filePath";

        if (! is_file($path)) {
            Logger::debug("---- OpenRevisionsDirPathFile: file $filePath does not exist");
            return [];
        }
        $contents = file_get_contents($path);

        if ($contents === false) {
            Logger::debug("---- Failed to read file contents from $filePath");
            return [];
        }

        $result = json_decode($contents, true);

        if ($result === null || $result === false) {
            Logger::debug("---- Failed to decode JSON from $filePath");
            return [];
        }

        Logger::debug("---- OpenRevisionsDirPathFile File: $filePath.");

        return $result;
    }
    public function init()
    {
        $home = $this->envVar('HOME');

        if (! $this->RevisionsDirPath) {
            $this->RevisionsDirPath = $home
                ? $home . '/public_html/revisions_new1'
                : dirname(__DIR__) . '/revisions_new1';
        }

        // Initialize revisions directory if needed
        if (! is_dir($this->RevisionsDirPath)) {
            mkdir($this->RevisionsDirPath, 0755, true);
        }

        // Ensure JSON data files exist

        if (! file_exists($this->jsonFile)) {
            file_put_contents($this->jsonFile, '{}', LOCK_EX);
        }

        if (! file_exists($this->jsonFileAll)) {
            file_put_contents($this->jsonFileAll, '{}', LOCK_EX);
        }

    }
}
