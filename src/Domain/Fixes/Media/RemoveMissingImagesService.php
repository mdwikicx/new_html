<?php

/**
 * Missing image removal service
 *
 * Provides functionality for removing missing images from wikitext
 * using a configurable image existence checker.
 *
 * @package MDWiki\NewHtml\Domain\Fixes\Media
 */

namespace MDWiki\NewHtml\Domain\Fixes\Media;

use MDWiki\NewHtml\Domain\Parser\Template;
use MDWiki\NewHtml\Services\Interfaces\CommonsImageServiceInterface;

use function MDWiki\NewHtml\Domain\Parser\getTemplates;

class RemoveMissingImagesService
{
    private CommonsImageServiceInterface $imageService;

    /**
     * Constructor
     *
     * @param CommonsImageServiceInterface $imageService Service for checking image existence
     */
    public function __construct(CommonsImageServiceInterface $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Remove infobox images that don't exist on Commons
     *
     * @param string $text The wikitext to process
     * @return string The processed wikitext
     */
    public function removeMissingInfoboxImages(string $text): string
    {
        // First, try to parse templates using getTemplates
        $templates = getTemplates($text);

        // Process templates using the template parser
        foreach ($templates as $template) {
            $originalText = $template->getTemplateText();
            $params = $template->getParameters();
            $modified = false;

            // Check all image parameters (image, image2, image3, etc.)
            foreach ($params as $paramName => $paramValue) {
                // Match image parameters (case-insensitive)
                if (preg_match('/^image(\d*)$/i', $paramName, $matches)) {
                    $imageNumber = $matches[1]; // Could be empty string for 'image', or '2', '3', etc.
                    $filename = trim($paramValue);

                    // If empty or doesn't exist, remove it and corresponding caption
                    if (empty($filename) || !$this->imageService->imageExists($filename)) {
                        $template->deleteParameter($paramName);

                        // Also remove corresponding caption parameter
                        $captionParam = 'caption' . $imageNumber;
                        if (array_key_exists($captionParam, $params)) {
                            $template->deleteParameter($captionParam);
                        }

                        $modified = true;
                    }
                }
            }

            // Replace the original template with the modified one
            if ($modified) {
                $newText = $template->toString();
                $text = str_replace($originalText, $newText, $text);
            }
        }

        $text = $this->removeMissingInfoboxImagesRegex($text);

        return $text;
    }

    /**
     * Remove missing infobox images using regex (fallback)
     *
     * @param string $text The wikitext to process
     * @return string The processed wikitext
     */
    private function removeMissingInfoboxImagesRegex(string $text): string
    {
        // Also handle non-template infobox parameters using regex
        // This handles cases where infobox fields are not wrapped in a template
        $pattern = '/^\s*\|(\s*image\d*\s*)=([^\n]*)/m';

        // Collect fields to remove
        $fieldsToRemove = [];

        preg_replace_callback($pattern, function ($matches) use (&$fieldsToRemove) {
            $fullMatch = $matches[0];
            $fieldName = trim($matches[1]);
            $filename = trim($matches[2]);

            // If empty or doesn't exist, mark for removal
            if (empty($filename) || !$this->imageService->imageExists($filename)) {
                // Add both image and caption fields to removal list
                $fieldsToRemove[] = $fieldName;

                // The caption field would be like caption or caption2
                $number = preg_replace('/^image(\d*)$/i', '$1', $fieldName);
                $captionFieldName = 'caption' . $number;
                $fieldsToRemove[] = $captionFieldName;
            }

            return $fullMatch;
        }, $text);

        // Remove the marked fields
        foreach ($fieldsToRemove as $field) {
            $fieldPattern = '/^\s*\|\s*' . preg_quote($field, '/') . '\s*=[^\n]*\n?/m';
            $text = preg_replace($fieldPattern, '', $text);
        }
        return $text;
    }

    /**
     * Remove inline [[File:...]] or [[Image:...]] images that don't exist on Commons
     *
     * @param string $text The wikitext to process
     * @return string The processed wikitext
     */
    public function removeMissingInlineImages(string $text): string
    {
        // Pattern to match [[File:...]] or [[Image:...]] with proper bracket counting
        // This needs to handle nested [[links]] inside the caption

        $offset = 0;
        while (preg_match('/\[\[(File|Image):([^\]|]+)/i', $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $startPos = $matches[0][1];
            $prefix = $matches[1][0];
            $filename = $matches[2][0];

            // Find the matching closing brackets by counting bracket depth
            $bracketDepth = 2; // We start with [[
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

                // Check if the image exists
                if (!$this->imageService->imageExists($filename)) {
                    // Remove the entire image block
                    $text = substr($text, 0, $startPos) . substr($text, $endPos + 1);
                    $offset = $startPos;
                } else {
                    // Move past this image
                    $offset = $endPos + 1;
                }
            } else {
                // Malformed image tag, skip it
                $offset = $startPos + 1;
            }
        }

        return $text;
    }

    /**
     * Remove all missing images (both infobox and inline)
     *
     * @param string $text The wikitext to process
     * @return string The processed wikitext with missing images removed
     */
    public function removeMissingImages(string $text): string
    {
        $text = $this->removeMissingInfoboxImages($text);
        $text = $this->removeMissingInlineImages($text);
        return $text;
    }
}
