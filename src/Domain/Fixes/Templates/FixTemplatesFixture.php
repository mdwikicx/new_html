<?php

namespace MDWiki\NewHtml\Domain\Fixes\Templates;

use function MDWiki\NewHtml\Domain\Parser\getTemplates;

function add_missing_title(string $text, string $title, int $ljust = 17): string
{

    $temps = [
        "drug box" => "drug_name",
        "drugbox" => "drug_name",
        "infobox drug" => "drug_name",
        "infobox medical condition" => "name",
        "infobox medical intervention" => "name",

    ];

    $temps_in = getTemplates($text);

    $new_text = $text;

    foreach ($temps_in as $temp) {

        $name = strtolower($temp->getStripName());

        if (!isset($temps[$name])) {
            continue;
        }

        $old_text_template = $temp->getTemplateText();

        $param = $temps[$name];

        $name_p = $temp->getParameter($param, "");

        if (!$name_p || empty(trim($name_p))) {
            $temp->setParameter($param, $title);
        }

        $new_text_str = $temp->toString(true, $ljust);

        $new_text = str_replace($old_text_template, $new_text_str, $new_text);
    };

    return $new_text;
}
