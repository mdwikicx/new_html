<?php

namespace MDWiki\NewHtml\Services\Api;

use function MDWiki\NewHtml\Services\Api\handle_url_request;

function check_commons_image_exists(string $filename): bool
{
    if (empty(trim($filename))) {
        return false;
    }

    $filename = preg_replace('/^(File|Image):/i', '', $filename);
    $filename = trim($filename);

    if (empty($filename)) {
        return false;
    }
    $params = [
        'action' => 'query',
        'titles' => 'File:' . $filename,
        'format' => 'json'
    ];

    $url = "https://commons.wikimedia.org/w/api.php";

    $response = handle_url_request($url, 'GET', $params);

    if ($response === "") {
        return true;
    }

    $json = json_decode($response, true);
    foreach ($json['query']['pages'] ?? [] as $page) {
        return !isset($page['missing']);
    }
    return false;
}
