<?php

declare(strict_types=1);

namespace App\Interfaces;
use DateTime;

interface LlmServiceInterface
{
    public function makePost(DateTime $date): array;
    public function makeImage(string $article): array;
}