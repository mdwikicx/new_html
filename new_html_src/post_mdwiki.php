<?php

namespace PostMdwiki;
/*

use function PostMdwiki\handle_url_request_mdwiki;

*/
// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

use function Printn\test_print;

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';

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

    if (!isset($_GET['cacert'])) {
        $caFile = __DIR__ . '/../cacert.pem';
        // ---
        if (file_exists($caFile)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caFile);
            test_print("<br>CURLOPT_CAINFO: cacert.pem");
        } else {
            test_print("<br>Warning: CA certificate file not found");
        }
    }

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
