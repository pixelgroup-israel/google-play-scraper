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
use Pixelgroup\GPlay\Model\AppId;
use Pixelgroup\GPlay\Model\Review;
use Pixelgroup\GPlay\Scraper\Extractor\ReviewsExtractor;
use Pixelgroup\GPlay\Util\ScraperUtil;
use Pixelgroup\HttpClient\ResponseHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\parse_query;

/**
 * @internal
 */
class AppSpecificReviewScraper implements ResponseHandlerInterface
{
    /** @var AppId */
    private $requestApp;

    /**
     * OneAppReviewScraper constructor.
     *
     * @param AppId $requestApp
     */
    public function __construct(AppId $requestApp)
    {
        $this->requestApp = $requestApp;
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @throws GooglePlayException
     *
     * @return Review
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): Review
    {
        $reviewId = parse_query($request->getUri()->getQuery())['reviewId'];
        $scriptData = ScraperUtil::extractScriptData($response->getBody()->getContents());

        foreach ($scriptData as $key => $value) {
            if (isset($value[0][0][0]) && $value[0][0][0] === $reviewId) {
                return ReviewsExtractor::extractReview($this->requestApp, $value[0][0]);
            }
        }

        throw new GooglePlayException(
            sprintf('%s application review %s does not exist.', $this->requestApp->getId(), $reviewId)
        );
    }
}
