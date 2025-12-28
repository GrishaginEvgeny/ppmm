<?php

namespace App\Enum;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

enum DirectionName: string
{
    case SPORT = 'Спортивное';
    case SCIENCE = 'Научно-исследовательское';
    case PEOPLE = 'Общественное';
    case CULTURE = 'Культурно-развлекательное';
    case STUDY = 'Академическое';

    public static function getNameByUrl(LadderUrl $url)
    {
        return match ($url) {
            LadderUrl::SCIENCE => self::SCIENCE,
            LadderUrl::SPORT => self::SPORT,
            LadderUrl::STUDY => self::STUDY,
            LadderUrl::CULTURE => self::CULTURE,
            LadderUrl::PEOPLE => self::PEOPLE,
            default => throw new NotFoundHttpException(),
        };
    }
}
