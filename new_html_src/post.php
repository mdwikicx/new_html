<?php

namespace Post;
/*

use function Post\handle_url_request;
use function Post\post_url_params_result;

*/
// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

use function Printn\test_print;

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';

function post_url_params_result(string $endPoint, array $params = []): string
{
    global $usr_agent;
    $ch = curl_init();

    $url = "{$endPoint}";

    curl_setopt($ch, CURLOPT_URL, $endPoint);

    if (count($params) > 0) {
        $url = "{$endPoint}?" . http_build_query($params);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    test_print($url);

    $output = curl_exec($ch);
    if ($output === FALSE) {
        test_print("endPoint: ($endPoint), cURL Error: " . curl_error($ch));
        curl_close($ch);
        return '';
    }
    // Check HTTP response code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        test_print("API returned HTTP $http_code: $http_code");
        // return ['error' => "Error: API returned HTTP $http_code"];
    }
    curl_close($ch);
    return $output;
}

function handle_url_request(string $endPoint, string $method = 'GET', array $params = []): string
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
