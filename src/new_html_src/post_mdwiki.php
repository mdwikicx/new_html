<?php

/**
 * MDWiki HTTP request services (Backward Compatibility)
 *
 * @deprecated Use MDWiki\NewHtml\Application\Handlers\PostMdwikiHandler instead
 * @package MDWiki\NewHtml
 */

namespace PostMdwiki;

use function MDWiki\NewHtml\Application\Handlers\handle_url_request_mdwiki as new_handle_url_request_mdwiki;

/**
 * @deprecated Use MDWiki\NewHtml\Application\Handlers\handle_url_request_mdwiki
 * @param array<string, mixed> $params
 */
function handle_url_request_mdwiki(string $endPoint, string $method = 'GET', array $params = []): string
{
    return new_handle_url_request_mdwiki($endPoint, $method, $params);
}
