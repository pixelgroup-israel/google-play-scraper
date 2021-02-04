<?php

declare(strict_types=1);

/**
 * @author   Pixel Group
 * @license  MIT
 *
 * @see      https://github.com/pixel-group/google-play-scraper
 */

namespace Pixelgroup\GPlay\Scraper;

use Pixelgroup\GPlay\GPlayApps;
use Pixelgroup\GPlay\Scraper\Extractor\AppsExtractor;
use Pixelgroup\HttpClient\ResponseHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\parse_query;

/**
 * @internal
 */
class PlayStoreUiAppsScraper implements ResponseHandlerInterface
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): array
    {
        $contents = substr($response->getBody()->getContents(), 5);
        $json = \GuzzleHttp\json_decode($contents, true);

        if (empty($json[0][2])) {
            return [[], null];
        }
        $json = \GuzzleHttp\json_decode($json[0][2], true);

        if (empty($json[0][0][0])) {
            return [[], null];
        }

        $query = parse_query($request->getUri()->getQuery());
        $locale = $query[GPlayApps::REQ_PARAM_LOCALE] ?? GPlayApps::DEFAULT_LOCALE;
        $country = $query[GPlayApps::REQ_PARAM_COUNTRY] ?? GPlayApps::DEFAULT_COUNTRY;

        $apps = [];

        foreach ($json[0][0][0] as $data) {
            $apps[] = AppsExtractor::extractApp($data, $locale, $country);
        }

        $nextToken = $json[0][0][7][1] ?? null;

        return [$apps, $nextToken];
    }
}
