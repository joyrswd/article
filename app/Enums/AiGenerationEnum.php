<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\RandomEnumTrait;

enum AiGenerationEnum:string
{
    use RandomEnumTrait;

    case Child = '子供';
    case Young = '若者';
    case Middle = '中年';
    case Senior = '老人';
}