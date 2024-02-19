<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiPersonalityEnum:string
{
    use RandomEnumTrait;

    case Cheerful = '快活';
    case Ironic = '皮肉屋';
    case Noble = '高貴';
    case Roughly = '粗野';
}