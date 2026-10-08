<?php

final class PageResult
{
    public function __construct(
        public readonly int $status,
        public readonly string $format, // json | wikitext | html | seg
        public readonly string | array $body,
    ) {}
}
