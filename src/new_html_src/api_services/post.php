<?php

/**
 * HTTP request handling services
 *
 * Backward compatibility wrapper for Services\Api\HttpClient
 *
 * @deprecated Use MDWiki\NewHtml\Services\Api\HttpClient instead
 * @package MDWiki\NewHtml\APIServices
 */

namespace APIServices;

use function MDWiki\NewHtml\Services\Api\post_url_params_result as NewPostUrlParamsResult;
use function MDWiki\NewHtml\Services\Api\handle_url_request as NewHandleUrlRequest;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\post_url_params_result instead
 * @param array<string, mixed> $params
 */
function post_url_params_result(string $endPoint, array $params = []): string
{
    return NewPostUrlParamsResult($endPoint, $params);
}

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\handle_url_request instead
 * @param array<string, mixed> $params
 */
function handle_url_request(string $endPoint, string $method = 'GET', array $params = []): string
{
    return NewHandleUrlRequest($endPoint, $method, $params);
}
