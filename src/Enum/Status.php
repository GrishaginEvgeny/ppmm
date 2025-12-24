<?php

namespace App\Enum;

enum Status: string
{
    case ACCEPTED = 'одобрено';
    case ON_CHECK = 'на проверке';
    case DECLINED = 'отказано';
}
