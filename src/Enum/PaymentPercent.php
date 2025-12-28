<?php

namespace App\Enum;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

enum PaymentPercent: int
{
    case STUDY = 20;
    case SCIENCE = 30;
    case PEOPLE = 26;
    case SPORT_OR_CULTURE = 12;

    public static function getPercentByUrl(LadderUrl $url)
    {
        return match ($url) {
            LadderUrl::SCIENCE => self::SCIENCE,
            LadderUrl::SPORT, LadderUrl::CULTURE => self::SPORT_OR_CULTURE,
            LadderUrl::STUDY => self::STUDY,
            LadderUrl::PEOPLE => self::PEOPLE,
            default => throw new NotFoundHttpException(),
        };
    }
}
