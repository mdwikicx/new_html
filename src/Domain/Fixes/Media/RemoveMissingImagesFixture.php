<?php

namespace MDWiki\NewHtml\Domain\Fixes\Media;

use function APIServices\check_commons_image_exists;
use function MDWiki\NewHtml\Domain\Parser\getTemplates;

function remove_missing_infobox_images(string $text): string
{
    $templates = getTemplates($text);

    foreach ($templates as $template) {
        $originalText = $template->getTemplateText();
        $params = $template->getParameters();
        $modified = false;

        foreach ($params as $paramName => $paramValue) {
            if (preg_match('/^image(\d*)$/i', $paramName, $matches)) {
                $imageNumber = $matches[1];
                $filename = trim($paramValue);

                if (empty($filename) || !check_commons_image_exists($filename)) {
                    $template->deleteParameter($paramName);

                    $captionParam = 'caption' . $imageNumber;
                    if (array_key_exists($captionParam, $params)) {
                        $template->deleteParameter($captionParam);
                    }

                    $modified = true;
                }
            }
        }

        if ($modified) {
            $newText = $template->toString();
            $text = str_replace($originalText, $newText, $text);
        }
    }

    $pattern = '/^\s*\|(\s*image\d*\s*)=([^\n]*)/m';

    $fieldsToRemove = [];

    preg_replace_callback($pattern, function ($matches) use (&$fieldsToRemove) {
        $fullMatch = $matches[0];
        $fieldName = trim($matches[1]);
        $filename = trim($matches[2]);

        if (empty($filename) || !check_commons_image_exists($filename)) {
            $fieldsToRemove[] = $fieldName;

            $number = preg_replace('/^image(\d*)$/i', '$1', $fieldName);
            $captionFieldName = 'caption' . $number;
            $fieldsToRemove[] = $captionFieldName;
        }

        return $fullMatch;
    }, $text);

    foreach ($fieldsToRemove as $field) {
        $fieldPattern = '/^\s*\|\s*' . preg_quote($field, '/') . '\s*=[^\n]*\n?/m';
        $text = preg_replace($fieldPattern, '', $text);
    }

    return $text;
}

function remove_missing_inline_images(string $text): string
{
    $offset = 0;
    while (preg_match('/\[\[(File|Image):([^\]|]+)/i', $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
        $startPos = $matches[0][1];
        $prefix = $matches[1][0];
        $filename = $matches[2][0];

        $bracketDepth = 2;
        $pos = $startPos + strlen($matches[0][0]);
        $endPos = false;

        while ($pos < strlen($text) && $bracketDepth > 0) {
            if ($text[$pos] === '[' && isset($text[$pos + 1]) && $text[$pos + 1] === '[') {
                $bracketDepth += 2;
                $pos += 2;
            } elseif ($text[$pos] === ']' && isset($text[$pos + 1]) && $text[$pos + 1] === ']') {
                $bracketDepth -= 2;
                if ($bracketDepth === 0) {
                    $endPos = $pos + 1;
                    break;
                }
                $pos += 2;
            } else {
                $pos++;
            }
        }

        if ($endPos !== false) {
            $fullImageBlock = substr($text, $startPos, $endPos - $startPos + 1);

            if (!check_commons_image_exists($filename)) {
                $text = substr($text, 0, $startPos) . substr($text, $endPos + 1);
                $offset = $startPos;
            } else {
                $offset = $endPos + 1;
            }
        } else {
            $offset = $startPos + 1;
        }
    }

    return $text;
}

function remove_missing_images(string $text): string
{
    $text = remove_missing_infobox_images($text);
    $text = remove_missing_inline_images($text);
    return $text;
}
