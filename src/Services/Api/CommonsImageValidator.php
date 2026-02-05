<?php

/**
 * Commons Image Validator Implementation
 *
 * Implements ImageValidatorInterface using Wikimedia Commons API.
 * This adapter wraps the existing check_commons_image_exists() function.
 *
 * @package MDWiki\NewHtml\Services\Api
 */

namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Domain\Contracts\ImageValidatorInterface;
use function MDWiki\NewHtml\Services\Api\check_commons_image_exists;

/**
 * Validates images using Wikimedia Commons API
 */
class CommonsImageValidator implements ImageValidatorInterface
{
    /**
     * Check if an image exists on Wikimedia Commons
     *
     * @param string $imageName The name of the image to check (without File: prefix)
     * @return bool True if the image exists, false otherwise
     */
    public function imageExists(string $imageName): bool
    {
        return check_commons_image_exists($imageName);
    }
}
