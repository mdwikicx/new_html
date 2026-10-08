<?php
namespace MDWiki\NewHtml\DTO;

/**
 * Typed, validated representation of an incoming request.
 *
 * Request parameters:
 * - title:     page title (already normalized by the controller)
 * - new:       force regeneration of cached content
 * - all:       use the 'all' data file
 * - printetxt: output format (wikitext|html|seg), alias: print
 */
final class PageRequest
{
    public const FORMAT_JSON     = 'json';
    public const FORMAT_WIKITEXT = 'wikitext';
    public const FORMAT_HTML     = 'html';
    public const FORMAT_SEG      = 'seg';

    private const VALID_FORMATS = [
        self::FORMAT_WIKITEXT,
        self::FORMAT_HTML,
        self::FORMAT_SEG,
    ];

    public function __construct(
        public readonly string $title,
        public readonly string $format,
        public readonly string $all,
        public readonly bool $new,
    ) {}

    /**
     * @param array<string, mixed> $request usually $_GET
     */
    public static function fromArray(array $request, string $title): self
    {
        $format = (string) ($request['printetxt'] ?? $request['print'] ?? '');
        if (! in_array($format, self::VALID_FORMATS, true)) {
            $format = self::FORMAT_JSON;
        }

        $all = (string) ($request['all'] ?? '');

        // Video pages always use the 'all' data file
        if (str_starts_with($title, 'Video')) {
            $all = '1';
        }

        return new self(
            title: $title,
            format: $format,
            all: $all,
            new : isset($request['new']),
        );
    }
}
