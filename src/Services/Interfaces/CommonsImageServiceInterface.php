<?php

/**
 * Commons Image Service Interface
 *
 * @package MDWiki\NewHtml\Services\Interfaces
 */

namespace MDWiki\NewHtml\Services\Interfaces;

interface CommonsImageServiceInterface
{
    /**
     * Check if an image exists on Wikimedia Commons
     *
     * @param string $filename The filename to check (without File: prefix)
     * @return bool True if the image exists, false otherwise
     */
    public function imageExists(string $filename): bool;
}
