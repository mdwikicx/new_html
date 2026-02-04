<?php

/**
 * Wikipedia transform API services
 *
 * Backward compatibility wrapper for Services\Api\TransformApiService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Api\TransformApiService instead
 * @package MDWiki\NewHtml\APIServices
 */

namespace APIServices;

use function MDWiki\NewHtml\Services\Api\convert_wikitext_to_html as NewConvertWikitextToHtml;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\convert_wikitext_to_html instead
 * @return array<string, string>
 */
function convert_wikitext_to_html(string $text, string $title): array
{
    return NewConvertWikitextToHtml($text, $title);
}
