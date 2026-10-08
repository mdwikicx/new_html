<?php
namespace MDWiki\NewHtml\DTO;

/**
 * Result of processing a page. Contains no output side effects:
 * the controller decides how to send it.
 */
final class PageResult
{
    /**
     * @param string|array<string, mixed> $body string for wikitext/html/seg, array for json
     */
    public function __construct(
        public readonly int $status,
        public readonly string $format,
        public readonly string | array $body,
    ) {}

    /** @param array<string, mixed> $data */
    public static function json(int $status, array $data): self
    {
        return new self($status, PageRequest::FORMAT_JSON, $data);
    }

    public static function text(string $format, string $body, int $status = 200): self
    {
        return new self($status, $format, $body);
    }
}
