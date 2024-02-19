<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiGenreEnum:string
{
    use RandomEnumTrait;

    case Sports = 'スポーツ';
    case Travel = '旅行';
    case History = '歴史';
    case Food = 'グルメ';
    case Tech = 'テクノロジー';
    case Animal = '動物';
    case Health = '健康';
}