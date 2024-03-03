<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AiImageServiceInterface
{
    public function makeImage(string $article): string;
    public function getImageModel() : string;
}