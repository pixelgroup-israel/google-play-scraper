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
 * Contains all valid values for the "price" parameter.
 *
 * @method static PriceEnum ALL()  Returns the value of the price parameter for all apps.
 * @method static PriceEnum FREE() Returns the value of the price parameter for free apps.
 * @method static PriceEnum PAID() Returns the value of the price parameter for paid apps.
 */
class PriceEnum extends Enum
{
    private const ALL = 0;

    private const FREE = 1;

    private const PAID = 2;
}
