<?php

/**
 * MDWiki HTTP request services
 *
 * Provides functions for making HTTP requests to MDWiki API endpoints,
 * supporting both GET and POST methods with cURL.
 *
 * @package MDWiki\NewHtml
 */

namespace MDWiki\NewHtml\Application\Handlers;
/*

use function MDWiki\NewHtml\Application\Handlers\handle_url_request_mdwiki;

*/
// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';

/**
 * Handle URL requests to MDWiki with support for GET and POST methods
 *
 * @param string $endPoint The API endpoint URL
 * @param string $method The HTTP method to use ('GET' or 'POST')
 * @param array<string, mixed> $params Optional parameters to send with the request
 * @return string The response body, or empty string on failure
 */
function handle_url_request_mdwiki(string $endPoint, string $method = 'GET', array $params = []): string
{
    global $usr_agent;

    $ch = curl_init();

    $url = $endPoint;

    if (!empty($params) && $method === 'GET') {
        $query_string = http_build_query($params);
        $url = strpos($url, '?') === false ? "$url?$query_string" : "$url&$query_string";
        $endPoint = $url;
    }

    curl_setopt($ch, CURLOPT_URL, $endPoint);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    test_print($url);

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
