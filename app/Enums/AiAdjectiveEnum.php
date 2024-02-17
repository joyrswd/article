<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiAdjectiveEnum:string
{
    use RandomEnumTrait;

    case Passionate = '情熱的';
    case Cool = 'クール';
    case Humor = 'ユーモラス';
    case Serious = '真面目';
}