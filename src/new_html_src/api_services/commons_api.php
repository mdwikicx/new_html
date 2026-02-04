<?php

/**
 * Wikimedia Commons API services
 *
 * Backward compatibility wrapper for Services\Api\CommonsApiService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Api\CommonsApiService instead
 * @package MDWiki\NewHtml\APIServices
 */

namespace APIServices;

use function MDWiki\NewHtml\Services\Api\check_commons_image_exists as NewCheckCommonsImageExists;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\check_commons_image_exists instead
 */
function check_commons_image_exists(string $filename): bool
{
    return NewCheckCommonsImageExists($filename);
}
