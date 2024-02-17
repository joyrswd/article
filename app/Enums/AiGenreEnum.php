<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiGenreEnum:string
{
    use RandomEnumTrait;

    case History = '歴史';
    case Entertainment = 'エンタメ';
    case Economics = '経済';
    case Sports = 'スポーツ';
}