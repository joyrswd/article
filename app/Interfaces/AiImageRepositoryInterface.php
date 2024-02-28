<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AiImageRepositoryInterface
{
    public function makeImage(string $message) : array;
    public function getUrl(array $response) : string;
    public function getDescription(array $response) : string;
    public function getSize() : string;
    public function getModel() : string;
}