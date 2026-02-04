<!DOCTYPE html>
<HTML lang=en dir=ltr data-bs-theme="light" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WikiProjectMed Tools</title>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css'>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.7.0/jquery.min.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js'></script>
    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>

<?php
define("DEBUGX", true);
require_once __DIR__ . "/require.php";

use function FixText\fix_wikitext;

$text = $_POST['text'] ?? '';
$title = $_POST['title'] ?? '';

$msg = "";

if ($text && $title) {
    $changed_text = fix_wikitext($text, $title);
    if ($changed_text == $text) {
        $msg = <<<HTML
            <div class="alert alert-warning" role="alert">
                No changes made.
            </div>
        HTML;
    } else {
        $text = $changed_text;
        $msg = <<<HTML
            <div class="alert alert-success" role="alert">
                Changes made.
            </div>
        HTML;
    }
}

?>
<div class="container">
    <div class='card'>
        <div class='card-header aligncenter' style='font-weight:bold;'>
            input infos
        </div>
        <div class='card-body'>
            <form action='fix.php' method='POST'>
                <div class='container'>
                    <div class='row'>
                        <div class='col-md-3'>
                            <div class='input-group mb-3'>
                                <div class='input-group-prepend'>
                                    <span class='input-group-text'>title</span>
                                </div>
                                <input class='form-control' type='text' id='title' name='title' value='<?php echo $title; ?>' />
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <h4 class='aligncenter'>
                                <input class='btn btn-outline-primary' type='submit' value='start'>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <textarea id="text" name="text" rows="5" class="form-control" required><?php echo $text; ?></textarea>
                </div>
            </form>
            <?php echo $msg; ?>
        </div>
    </div>
</div>
