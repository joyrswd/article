<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ApiRepositoryInterface
{
    public function requestApi(): mixed;
    public function getModel(): string;
    public function setContent(mixed $content): void;
}