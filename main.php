<?php
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/require.php";

use function Printn\test_print;
use function Wikitext\get_wikitext;
use function Segments\html_to_seg;
use function Html\wiki_text_to_html;
use function HtmlFixes\remove_data_parsoid;
use function NewHtml\FileHelps\get_file_dir;
use function NewHtml\FileHelps\file_write;
use function NewHtml\JsonData\get_from_json;

$printetxt = $_GET['printetxt'] ?? $_GET['print'] ?? '';

$content_types = [
    "wikitext" => "text/plain",
    "html" => "text/html",
    "seg" => "text/html",
];

$content_type = $content_types[$printetxt] ?? "application/json";

header("Content-type: $content_type");

function get_title()
{
    $title = $_GET['title'] ?? '';
    // ---
    // first litter in $title must be capital
    $title = ucfirst($title);
    // ---
    return $title;
}

function error_1($title, $revision)
{
    // send request error code using http_response_code
    http_response_code(404);
    // ---
    $data = [
        "sourceLanguage" => "en",
        "title" => $title,
        "revision" => $revision,
        "segmentedContent" => "",
        "categories" => [],
        "error_type" => "title:($title) or revision:($revision) not found",
        "error" => "No content found!",
    ];
    // ---
    return json_encode($data);
}

function get_wikitext_revision($title, $all)
{
    global $printetxt;
    // ---
    $from_cache = false;
    // ---
    // test_print("title: $title, all: $all, printetxt: $printetxt");
    // ---
    [$wikitext, $revision] = get_wikitext($title, $all);
    // ---
    if ($wikitext == '' || $revision == '') {
        [$wikitext, $revision] = get_from_json($title, $all);
        $from_cache = $wikitext != '';
    }
    // ---
    if ($printetxt == "wikitext") {
        // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=wikitext
        echo $wikitext;
        exit();
    }
    // ---
    return [$wikitext, $revision, $from_cache];
}

function get_HTML_text($wikitext, $file_html, $title, $new)
{
    global $printetxt;
    // ---
    $from_cache = false;
    // ---
    try {
        // ---
        [$HTML_text, $from_cache] = wiki_text_to_html($wikitext, $file_html, $title, $new);
        // ---
        $HTML_text = remove_data_parsoid($HTML_text);
    } catch (Exception $e) {
        test_print("HTML generation failed for title: $title. Error: " . $e->getMessage());
        http_response_code(500);
        exit(json_encode(['error' => 'Failed to generate HTML content']));
    }
    // ---
    if ($HTML_text == $wikitext) {
        $HTML_text = '';
    }
    // ---
    if ($printetxt == "html") {
        // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=html
        echo $HTML_text;
        exit();
    }
    // ---
    return [$HTML_text, $from_cache];
}

function get_SEG_text($HTML_text, $file_seg)
{
    global $printetxt;
    // ---
    $from_cache = false;
    $SEG_text = "";
    // ---
    if (!empty($HTML_text)) {
        [$SEG_text, $from_cache] = html_to_seg($HTML_text, $file_seg);
        // ---
        $SEG_text = remove_data_parsoid($SEG_text);
    }
    // ---
    if ($printetxt == "seg") {
        // https://medwiki.toolforge.org/new_html/index.php?title=Trifluoperazine&printetxt=seg
        echo $SEG_text;
        exit();
    }
    // ---
    return [$SEG_text, $from_cache];
}

function start($request, $title)
{
    // ---
    $new = isset($request['new']);
    // ---
    $all = $request['all'] ?? '';
    // if $title startwith Video then $all = 1
    if (strpos($title, 'Video') === 0) {
        $all = "1";
    }
    // ---
    $cache_data = [
        'wikitext' => false,
        'html' => false,
        'seg' => false
    ];
    // ---
    [$wikitext, $revision, $text_cache] = get_wikitext_revision($title, $all);
    // ---
    $cache_data['wikitext'] = $text_cache;
    // ---
    // $revision = (isset($request['revision'])) ? $request['revision'] : $revision;
    // ---
    if ($wikitext == '' || $revision == '') {
        exit(error_1($title, $revision));
    }
    // ---
    $file_dir = get_file_dir($revision, $all);
    // ---
    $file_wikitext = $file_dir . "/wikitext.txt";
    $file_html     = $file_dir . "/html.html";
    $file_seg      = $file_dir . "/seg.html";
    $file_title    = $file_dir . "/title.txt";
    // ---
    file_write($file_wikitext, $wikitext);
    // ---
    file_write($file_title, $title);
    // ---
    [$HTML_text, $html_cache] = get_HTML_text($wikitext, $file_html, $title, $new);
    // ---
    $cache_data['html'] = $html_cache;
    // ---
    $SEG_text = "";
    // ---
    // print_data($revision, $SEG_text, $sourcelanguage, $title, $error = $error);
    $jsonData = [

        "cache_data" => $cache_data,
        "sourceLanguage" => "en",
        "title" => $title,
        "revision" => $revision,
        "segmentedContent" => $SEG_text,
        "categories" => []
    ];
    // ---
    if (empty($HTML_text)) {
        $jsonData['error_type'] = "HTML_text:() is empty";
        $jsonData['error'] = "No content found";
    } else {
        [$SEG_text, $seg_cache] = get_SEG_text($HTML_text, $file_seg);
        // ---
        $jsonData['cache_data']['seg'] = $seg_cache;
        // ---
        $jsonData['segmentedContent'] = $SEG_text;
        // ---
        if ($SEG_text == "") {
            // send request error code using http_response_code
            http_response_code(404);
            $jsonData['error_type'] = "SEG_text:($SEG_text) is empty";
            $jsonData['error'] = "No content found";
        }
    }
    // ---
    // Encode data as JSON with appropriate options
    $jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    // ---
    // Output the JSON
    echo $jsonOutput;
}

$title = get_title();

if ($title == '') {
    header("Content-type: application/json");
    echo json_encode([
        'error' => 'title is empty',
    ]);
    exit;
}

start($_GET, $title);
