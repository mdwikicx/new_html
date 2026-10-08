<?php
// src/app/Logger.php
declare (strict_types = 1);

namespace MDWiki\NewHtml;

final class Logger
{
    private static ?bool $debug = null;
    private static function write(string $message): void
    {
        $isTesting = (getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? '')) === 'testing';
        if ($isTesting) {
            return;
        }
        error_log($message);
    }

    private static function isDebug(): bool
    {
        return self::$debug ??= ((getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? '')) === 'development');
    }

    public static function debug(mixed $s): void
    {
        if (self::isDebug()) {
            self::write('[debug] ' . (is_string($s) ? $s : print_r($s, true)));
        }
    }

    public static function error(string $message): void
    {
        self::write($message);
    }
}
