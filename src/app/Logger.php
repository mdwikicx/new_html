<?php
// src/app/Logger.php
declare (strict_types = 1);

namespace MDWiki\NewHtml;

final class Logger
{
    private static ?bool $debug = null;

    public static function isDebugOld(): bool
    {
        if (self::$debug === null) {
            if (isset($_COOKIE['test']) && $_COOKIE['test'] === 'x') {
                self::$debug = false;
            } else {
                self::$debug = isset($_REQUEST['test']) || isset($_COOKIE['test']);
            }
        }

        return self::$debug;
    }

    private static function isDebug(): bool
    {
        return self::$debug ??= ((getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? '')) === 'development');
    }

    public static function debug(mixed $s): void
    {
        if (self::isDebug()) {
            error_log('[debug] ' . (is_string($s) ? $s : print_r($s, true)));
        }
    }

    public static function error(string $message): void
    {
        error_log($message);
    }
}
