<?php

namespace MDWiki\NewHtml\Application\Handlers;

use function MDWiki\NewHtml\Services\Api\handle_url_request;

class PostMdwikiHandler
{
    public function handleRequest(string $url, string $method = 'GET', array $params = []): string
    {
        return handle_url_request($url, $method, $params);
    }
}
