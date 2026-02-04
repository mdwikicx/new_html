<?php

/**
 * MDWiki API services
 *
 * Backward compatibility wrapper for Services\Api\MdwikiApiService
 *
 * @deprecated Use MDWiki\NewHtml\Services\Api\MdwikiApiService instead
 * @package MDWiki\NewHtml\APIServices
 */

namespace APIServices;

use function MDWiki\NewHtml\Services\Api\get_wikitext_from_mdwiki_api as NewGetWikitextFromMdwikiApi;
use function MDWiki\NewHtml\Services\Api\get_wikitext_from_mdwiki_restapi as NewGetWikitextFromMdwikiRestapi;

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\get_wikitext_from_mdwiki_api instead
 */
function get_wikitext_from_mdwiki_api(string $title): array
{
    return NewGetWikitextFromMdwikiApi($title);
}

/**
 * @deprecated Use MDWiki\NewHtml\Services\Api\get_wikitext_from_mdwiki_restapi instead
 */
function get_wikitext_from_mdwiki_restapi(string $title): array
{
    return NewGetWikitextFromMdwikiRestapi($title);
}
