<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AiImageRepositoryInterface
{
    public function makeImage(string $message) : array;
    public function getImageUrl(array $response) : string;
    public function getImageDescription(array $response) : string;
    public function getImageSize() : string;
}