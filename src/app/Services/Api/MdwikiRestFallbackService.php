<?php
namespace MDWiki\NewHtml\Services\Api;

use MDWiki\NewHtml\Logger;

/**
 * Fetches REST wikitext from MDWiki, falling back to local files when the API fails.
 */
class MdwikiRestFallbackService
{
    public function __construct(
        private ?MdwikiApiService $api = null,
        private ?LocalRestJsonRepository $local = null,
    ) {
        $this->api ??= new MdwikiApiService();
        $this->local ??= new LocalRestJsonRepository();
    }

    /**
     * @return array{source: string, revid: string|int, error: string}
     */
    public function getWikitext(string $title): array
    {
        $result = $this->api->getWikitextFromMdwikiRestApi($title);

        if ($result['failed']) {
            Logger::error("MdwikiRestFallbackService: REST API failed ({$result['error']}) for title: $title, trying local file");

            $json = $this->local->find($title);
            if ($json !== null) {
                return [
                    'source' => $json['source'] ?? '',
                    'revid'  => $json['latest']['id'] ?? '',
                    'error'  => $result['error'],
                ];
            }
        }

        return ['source' => $result['source'], 'revid' => $result['revid'], 'error' => $result['error']];
    }
}
