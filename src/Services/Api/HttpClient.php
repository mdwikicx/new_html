<?php

/**
 * HTTP request handling services
 *
 * Provides functions for making HTTP requests to external APIs,
 * supporting both GET and POST methods with cURL.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;
/*

use function MDWiki\NewHtml\Services\Api\handleUrlRequest;

*/
// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;


/**
 * Handle URL requests with support for GET and POST methods
 *
 * @param string $endPoint The API endpoint URL
 * @param string $method The HTTP method to use ('GET' or 'POST')
 * @param array<string, mixed> $params Optional parameters to send with the request
 * @return string The response body, or empty string on failure
 */

function handleUrlRequest(string $endPoint, string $method = 'GET', array $params = []): string
{
    $ch = curl_init();
    $user_agent = defined('USER_AGENT') ? USER_AGENT : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
    $printable_url = $endPoint;
    // POST with parameters should not have the parameters in the URL
    // GET with parameters should have the parameters in the URL
    if (!empty($params) && $method === 'GET') {
        $query_string = http_build_query($params);
        $printable_url = strpos($printable_url, '?') === false ? "$printable_url?$query_string" : "$printable_url&$query_string";
        $endPoint = $printable_url;
    }

    curl_setopt($ch, CURLOPT_URL, $endPoint);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    test_print($printable_url);

    $output = curl_exec($ch);

    if ($output === false) {
        test_print("endPoint: ($endPoint), cURL Error: " . curl_error($ch));
        curl_close($ch);
        return '';
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        test_print("API returned HTTP $http_code: $http_code");
        test_print(var_export($output, true));
        $output = '';
    }

    curl_close($ch);

    return $output;
}
