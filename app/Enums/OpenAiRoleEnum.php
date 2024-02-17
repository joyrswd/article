<?php

declare(strict_types=1);

namespace App\Enums;

enum OpenAiRoleEnum: string
{
    case User = 'user';
    case System = 'system';
    case Assistant = 'assistant';
}