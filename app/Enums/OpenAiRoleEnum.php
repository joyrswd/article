<?php

declare(strict_types=1);

namespace App\Enums;

enum OpenAiRoleEnum: string
{
    case System = 'system';
    case User = 'user';
    case Assistant = 'assistant';
}