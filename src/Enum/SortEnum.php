<?php

/** @noinspection PhpUnusedPrivateFieldInspection */
declare(strict_types=1);

/**
 * @author   Pixel Group
 * @license  MIT
 *
 * @see      https://github.com/pixel-group/google-play-scraper
 */

namespace Pixelgroup\GPlay\Enum;

use Pixelgroup\Enum;

/**
 * Contains all valid values for the "sort" parameter.
 *
 * @method static SortEnum HELPFULNESS() Returns the value of the sorting of reviews by helpfulness.
 * @method static SortEnum NEWEST()      Returns the value of the sorting of reviews by newest.
 * @method static SortEnum RATING()      Returns the value of the sorting of reviews by rating.
 */
class SortEnum extends Enum
{
    /** @var int Most helpful first */
    private const HELPFULNESS = 1;

    /** @var int Newest */
    private const NEWEST = 2;

    /** @var int Rating */
    private const RATING = 3;
}
