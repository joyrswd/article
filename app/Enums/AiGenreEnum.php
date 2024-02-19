<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiGenreEnum:string
{
    use RandomEnumTrait;

    case Travel = '旅行';
    case Food = 'グルメ';
    case Health = '健康';
    case Sports = 'スポーツ';
    case Animal = '動物';
    case Plant = '植物';
    case Nature = '自然現象';
}