<?php

namespace APIServices;
/*
use function APIServices\get_wikitext_from_mdwiki_api;
use function APIServices\get_wikitext_from_mdwiki_restapi;

*/

use function PostMdwiki\handle_url_request_mdwiki;
// use function APIServices\post_url_params_result;
use function Printn\test_print;

function get_wikitext_from_mdwiki_api($title)
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

    // $req = post_url_params_result($url, $params);
    $req = handle_url_request_mdwiki($url, 'GET', $params);

    if (empty($req)) {
        test_print("Failed to fetch data from MDWiki API for title: $title");
        return ['', ''];
    }
    // ---
    $json1 = json_decode($req, true);
    // ---
    $revisions = $json1["query"]["pages"][0]["revisions"][0] ?? [];

    if (empty($revisions)) {
        test_print("No revision data found for title: $title");
        return ['', ''];
    }

    $source = $revisions["content"] ?? '';
    $revid = $revisions["revid"] ?? '';
    // ---
    return [$source, $revid];
}

function get_wikitext_from_mdwiki_restapi($title)
{
    $title2 = str_replace("/", "%2F", $title);
    $title2 = str_replace(" ", "_", $title2);
    $url = "https://mdwiki.org/w/rest.php/v1/page/" . $title2;

    // $req = post_url_params_result($url);
    $req = handle_url_request_mdwiki($url, 'GET');
    $json1 = json_decode($req, true);

    $source = $json1["source"] ?? '';
    $revid = $json1["latest"]["id"] ?? '';
    // ---
    return [$source, $revid];
}
