<?php

/**
 * HTTP Client Interface for API requests
 *
 * @package MDWiki\NewHtml\Services\Interfaces
 */

namespace MDWiki\NewHtml\Services\Interfaces;

interface HttpClientInterface
{
    public function handleRawRequest(string $endPoint, string $method = 'GET', array $params = [], bool $json = false, ?int $timeout = null, ?int $connectTimeout = null): array;
    public function request(string $endPoint, string $method = 'GET', array $params = [], bool $json = false, ?int $timeout = null, ?int $connectTimeout = null): array;
}
