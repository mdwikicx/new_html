<?php

/**
 * MDWiki API services
 *
 * Provides functions for fetching wikitext content from MDWiki
 * using both the API and REST API endpoints.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;

// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html


/**
 * Handle URL requests to MDWiki with support for GET and POST methods
 *
 * @param string $endPoint The API endpoint URL
 * @param string $method The HTTP method to use ('GET' or 'POST')
 * @param array<string, mixed> $params Optional parameters to send with the request
 * @return string The response body, or empty string on failure
 */
function handleUrlRequestMdwiki(string $endPoint, string $method = 'GET', array $params = []): string
{
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
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
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

/**
 * Get wikitext content from MDWiki API
 *
 * @param string $title The title of the page to fetch
 * @return array{0: string, 1: string|int} Array containing [content, revision_id]
 */
function getWikitextFromMdwikiApi(string $title): array
{
    $params = [
        "action" => "query",
        "format" => "json",
        "prop" => "revisions",
        "titles" => $title,
        "utf8" => 1,
        "formatversion" => "2",
        "rvprop" => "content|ids"
    ];
    $url = "https://mdwiki.org/w/api.php";

    $req = handleUrlRequestMdwiki($url, 'GET', $params);

    if (empty($req)) {
        test_print("Failed to fetch data from MDWiki API for title: $title");
        return ['', ''];
    }

    $json1 = json_decode($req, true);

    $revisions = $json1["query"]["pages"][0]["revisions"][0] ?? [];

    if (empty($revisions)) {
        test_print("No revision data found for title: $title");
        return ['', ''];
    }

    $source = $revisions["content"] ?? '';
    $revid = $revisions["revid"] ?? '';
    return [$source, $revid];
}

/**
 * Get wikitext content from MDWiki REST API
 *
 * @param string $title The title of the page to fetch
 * @return array{0: string, 1: string|int} Array containing [content, revision_id]
 */
function getWikitextFromMdwikiRestapi(string $title): array
{
    $titleEncoded = str_replace("/", "%2F", $title);
    $titleEncoded = str_replace(" ", "_", $titleEncoded);
    $url = "https://mdwiki.org/w/rest.php/v1/page/" . $titleEncoded;

    $req = handleUrlRequestMdwiki($url, 'GET');
    $json1 = json_decode($req, true);

    $source = $json1["source"] ?? '';
    $revid = $json1["latest"]["id"] ?? '';

    return [$source, $revid];
}
