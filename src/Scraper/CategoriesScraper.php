<?php

declare(strict_types=1);

/**
 * @author   Pixel Group
 * @license  MIT
 *
 * @see      https://github.com/pixel-group/google-play-scraper
 */

namespace Pixelgroup\GPlay\Scraper;

use Pixelgroup\GPlay\Exception\GooglePlayException;
use Pixelgroup\GPlay\Model\Category;
use Pixelgroup\GPlay\Util\ScraperUtil;
use Pixelgroup\HttpClient\ResponseHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class CategoriesScraper implements ResponseHandlerInterface
{
    private const CATEGORY_URL_PREFIX = '/store/apps/category/';

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @throws GooglePlayException
     *
     * @return mixed
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        $scriptData = ScraperUtil::extractScriptData($response->getBody()->getContents());

        $dataCategories = null;

        foreach ($scriptData as $key => $data) {
            if (isset($data[0][1][0][3][0]) && \is_array($data[0][1][0][3][0])) {
                $dataCategories = $data;
                break;
            }
        }

        if ($dataCategories === null) {
            throw (new GooglePlayException('Failed to get the list of categories.'))
                ->setUrl($request->getUri()->__toString())
            ;
        }

        $parseCategories = static function (array $items) use (&$parseCategories): array {
            return array_reduce(
                $items,
                static function ($results, $item) use (&$parseCategories) {
                    if (\is_array($item)) {
                        if (
                            \count($item) === 6 &&
                            strpos($item[0], self::CATEGORY_URL_PREFIX) === 0 &&
                            strpos($item[0], '?age=') === false
                        ) {
                            $id = basename($item[0]);
                            $categoryName = $item[1];
                            $results[] = new Category(
                                $id,
                                $categoryName
                            );
                        } else {
                            $results = array_merge($results, $parseCategories($item));
                        }
                    }

                    return $results;
                },
                []
            );
        };

        return $parseCategories($dataCategories[0][1][0][3]);
    }
}
