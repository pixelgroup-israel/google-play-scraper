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
use Pixelgroup\GPlay\Util\ScraperUtil;
use Pixelgroup\HttpClient\ResponseHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\parse_query;

/**
 * @internal
 */
class ClusterAppsScraper implements ResponseHandlerInterface
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): array
    {
        $scriptData = ScraperUtil::extractScriptData($response->getBody()->getContents());
        $scriptDataInfo = null;

        foreach ($scriptData as $scriptValue) {
            if (isset($scriptValue[0][1][0][0][0]) && \is_array($scriptValue[0][1][0][0][0])) {
                $scriptDataInfo = $scriptValue; // ds:3
                break;
            }
        }

        if ($scriptDataInfo === null) {
            return [[], null];
        }

        $query = parse_query($request->getUri()->getQuery());
        $locale = $query[GPlayApps::REQ_PARAM_LOCALE] ?? GPlayApps::DEFAULT_LOCALE;
        $country = $query[GPlayApps::REQ_PARAM_COUNTRY] ?? GPlayApps::DEFAULT_COUNTRY;

        $apps = [];

        foreach ($scriptDataInfo[0][1][0][0][0] as $data) {
            $apps[] = AppsExtractor::extractApp($data, $locale, $country);
        }

        $nextToken = $scriptDataInfo[0][1][0][0][7][1] ?? null;

        return [$apps, $nextToken];
    }
}
