<html lang="en">

<?php
if (defined('DEBUGX') && DEBUGX === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
function get_host(): string
{
    // $hoste = get_host();
    //---
    static $cached_host = null;
    //---
    if ($cached_host !== null) {
        return $cached_host; // استخدم القيمة المحفوظة
    }
    //---
    $hoste = ($_SERVER["SERVER_NAME"] == "localhost")
        ? "https://cdnjs.cloudflare.com"
        : "https://tools-static.wmflabs.org/cdnjs";
    //---
    if ($hoste == "https://tools-static.wmflabs.org/cdnjs") {
        $url = "https://tools-static.wmflabs.org";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // لا نريد تحميل الجسم
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // لمنع الطباعة

        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // المهلة القصوى للاتصال
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CDN-Checker)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // إذا فشل الاتصال أو لم تكن الاستجابة ضمن 200–399، نستخدم cdnjs
        if ($result === false || !empty($curlError) || $httpCode < 200 || $httpCode >= 400) {
            $hoste = "https://cdnjs.cloudflare.com";
        }
    }

    $cached_host = $hoste;

    return $hoste;
}

$hoste = get_host();

echo <<<HTML
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Articles</title>
        <link rel='stylesheet' href='$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css'>
        <link rel='stylesheet' href='$hoste/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css'>
        <link rel='stylesheet' href='$hoste/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css'>
        <link rel='stylesheet' href='$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.css'>
        <link rel='stylesheet' href='$hoste/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css'>
        <link rel='stylesheet' href='$hoste/ajax/libs/datatables.net-bs5/2.2.2/dataTables.bootstrap5.css'>

        <script src='$hoste/ajax/libs/jquery/3.7.0/jquery.min.js'></script>
        <script src='$hoste/ajax/libs/popper.js/2.11.8/umd/popper.min.js'></script>
        <script src='$hoste/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js'></script>
        <script src='$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script>
        <script src='$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js'></script>
        <script src='$hoste/ajax/libs/datatables.net/2.2.2/dataTables.js'></script>
        <script src='$hoste/ajax/libs/datatables.net-bs5/2.2.2/dataTables.bootstrap5.min.js'></script>
        <style>
            a {
                text-decoration: none;
            }
        </style>
    </head>
HTML;

require_once __DIR__ . "/new_html_src/print.php";
require_once __DIR__ . "/new_html_src/utils/files_utils.php";
require_once __DIR__ . "/new_html_src/json_data.php";

use function NewHtml\FileHelps\get_revisions_new_dir;
use function NewHtml\JsonData\get_Data;
use function NewHtml\JsonData\dump_both_data;

function make_badge(array $files, string $file): string
{
    // ---
    if (!in_array($file, $files)) {
        return "<span class='badge bg-danger'>Missing</span>";
    }
    // ---
    // return "<span class='badge bg-success'>OK</span>";
    return "";
}
// ---
$revisions_new_dir = get_revisions_new_dir();
// ---
$dirs = array_filter(glob($revisions_new_dir . '/*/'), 'is_dir');
// sort directories by last modified date
usort($dirs, function ($a, $b) {
    $timeA = is_file($a . '/wikitext.txt') ? filemtime($a . '/wikitext.txt') : filemtime($a);
    $timeB = is_file($b . '/wikitext.txt') ? filemtime($b . '/wikitext.txt') : filemtime($b);
    return $timeB - $timeA;
});
// ---
$tbody = '';
// ---
$number = 0;
// ---
$main_url = $_SERVER['REQUEST_URI'];
$main_url = str_replace('/revisions_new.php', '', $main_url);
// ---
$main_data = get_Data('');
$main_data_all = get_Data('all');
// ---
$make_dump = empty($main_data);
// ---
foreach ($dirs as $dir) {
    // ---
    $number += 1;
    // ---
    $wikitextFile = $dir . '/wikitext.txt';
    $lastModified = is_file($wikitextFile)
        ? date('Y-m-d H:i', filemtime($wikitextFile))
        : date('Y-m-d H:i', filemtime($dir));
    // ---
    $dir = rtrim($dir, '/');
    // ---
    $dir_path = basename($dir);
    $oldid_number = str_replace('_all', '', $dir_path);
    // ---
    $files = array_filter(glob("$dir/*"), 'is_file');
    // ---
    $files = array_map('basename', $files);
    // ---
    // if wikitext.txt in $files
    $wikitext_tag = make_badge($files, 'wikitext.txt');
    $html_tag = make_badge($files, 'html.html');
    $seg_tag = make_badge($files, 'seg.html');
    // ---
    $title = (is_file("$dir/title.txt")) ? file_get_contents("$dir/title.txt") : '';
    // ---
    $title = str_replace('_', ' ', $title);
    // ---
    if (!empty($title) && $make_dump && !empty($oldid_number)) {
        $id = (int)$oldid_number;
        if ($id > 0) {
            if (strpos($dir_path, '_all') !== false) {
                $main_data_all[$title] = $id;
            } else {
                $main_data[$title] = $id;
            }
        }
    }
    // ---
    $title = htmlspecialchars($title);
    // ---
    $url = "open.php?revid=$dir_path&file";
    // ---
    $re_create_td = (isset($_GET['re'])) ? <<<HTML
        <td>
            <a class="card-link" href="/new_html/index.php?new=1&title=$title" target="_blank">Re create</a>
        </td>
    HTML : "";
    // ---
    $tbody .= <<<HTML
        <tr>
            <td>$number</td>
            <td>$lastModified</td>
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?title=$title" target="_blank">$title</a>
            </td>
            $re_create_td
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?oldid=$oldid_number" target="_blank">$dir_path</a>
            </td>
            <td>
                <a class="card-link" href="$url=wikitext.txt" target="_blank">Wikitext</a> $wikitext_tag
            </td>
            <td>
                <a class="card-link" href="$url=html.html" target="_blank">Html</a> $html_tag
            </td>
            <td>
                <a class="card-link" href="$url=seg.html" target="_blank">Segments</a> $seg_tag
            </td>
        </tr>
    HTML;
}
// ---
if ($make_dump) {
    dump_both_data($main_data, $main_data_all);
}
// ---
$re_create_th = (isset($_GET['re'])) ? "<th>Re create</th>" : '';
// ---
?>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light-subtle">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MDWiki</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/get_html">get_html</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/mdtexts">mdtexts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/new_html">new_html</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <table class="table compact table-striped" id="main_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>lastModified</th>
                            <th>Title</th>
                            <?php echo $re_create_th; ?>
                            <th>Revision</th>
                            <th>Wikitext</th>
                            <th>Html</th>
                            <th>Segments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $tbody; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $('#main_table').DataTable({
            paging: false,
            lengthMenu: [
                [25, 50, 100, 200],
                [25, 50, 100, 200]
            ],
        });
    </script>



</body>

</html>
