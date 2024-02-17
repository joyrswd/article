<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiPersonalityEnum:string
{
    use RandomEnumTrait;

    case Friendly = 'フレンドリー';
    case Ironic = '皮肉屋';
    case Noble = '高貴';
    case Mysterious = '不思議';

}