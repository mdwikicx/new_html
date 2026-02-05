<?php

/**
 * Image validator interface
 *
 * Defines the contract for checking if images exist.
 * This interface allows the Domain layer to remain independent
 * of specific service implementations (Clean Architecture).
 *
 * @package MDWiki\NewHtml\Domain\Contracts
 */

namespace MDWiki\NewHtml\Domain\Contracts;

/**
 * Interface for validating image existence
 */
interface ImageValidatorInterface
{
    /**
     * Check if an image exists
     *
     * @param string $imageName The name of the image to check (without File: prefix)
     * @return bool True if the image exists, false otherwise
     */
    public function imageExists(string $imageName): bool;
}
