<?php

namespace WikiParse\Template;

/*
Usage:

use function WikiParse\Template\getTemplate;
use function WikiParse\Template\getTemplates;

*/

use WikiConnect\ParseWiki\ParserTemplate;
use WikiConnect\ParseWiki\ParserTemplates;

function getTemplate($text)
{
    $parser = new ParserTemplate($text);
    $temp = $parser->getTemplate();
    return $temp;
}

function getTemplates($text)
{
    if (empty($text)) {
        return [];
    }
    $parser = new ParserTemplates($text);
    $temps = $parser->getTemplates();
    return $temps;
}
