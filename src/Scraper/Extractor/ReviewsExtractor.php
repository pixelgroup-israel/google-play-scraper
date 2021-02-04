<?php

declare(strict_types=1);

/**
 * @author   Pixel Group
 * @license  MIT
 *
 * @see      https://github.com/pixel-group/google-play-scraper
 */

namespace Pixelgroup\GPlay\Scraper\Extractor;

use Pixelgroup\GPlay\Model\AppId;
use Pixelgroup\GPlay\Model\GoogleImage;
use Pixelgroup\GPlay\Model\ReplyReview;
use Pixelgroup\GPlay\Model\Review;
use Pixelgroup\GPlay\Util\DateStringFormatter;

/**
 * @internal
 */
class ReviewsExtractor
{
    /**
     * @param AppId $requestApp
     * @param array $data
     *
     * @return array
     */
    public static function extractReviews(AppId $requestApp, array $data): array
    {
        $reviews = [];

        foreach ($data as $reviewData) {
            $reviews[] = self::extractReview($requestApp, $reviewData);
        }

        return $reviews;
    }

    /**
     * @param AppId $requestApp
     * @param       $reviewData
     *
     * @return Review
     */
    public static function extractReview(AppId $requestApp, $reviewData): Review
    {
        $reviewId = $reviewData[0];
        $reviewUrl = $requestApp->getUrl() . '&reviewId=' . urlencode($reviewId);
        $userName = $reviewData[1][0];
        $avatar = (new GoogleImage($reviewData[1][1][3][2]))->setSize(64);
        $date = DateStringFormatter::unixTimeToDateTime($reviewData[5][0]);
        $score = $reviewData[2] ?? 0;
        $text = (string) ($reviewData[4] ?? '');
        $likeCount = $reviewData[6];

        $reply = self::extractReplyReview($reviewData);

        return new Review(
            $reviewId,
            $reviewUrl,
            $userName,
            $text,
            $avatar,
            $date,
            $score,
            $likeCount,
            $reply
        );
    }

    /**
     * @param array $reviewData
     *
     * @return ReplyReview|null
     */
    private static function extractReplyReview(array $reviewData): ?ReplyReview
    {
        if (isset($reviewData[7][1])) {
            $replyText = $reviewData[7][1];
            $replyDate = DateStringFormatter::unixTimeToDateTime($reviewData[7][2][0]);

            if ($replyText && $reviewData) {
                return new ReplyReview(
                    $replyDate,
                    $replyText
                );
            }
        }

        return null;
    }
}
