<?php

declare(strict_types=1);

namespace App\Interfaces;

interface LlmRepositoryInterface
{
    public function makeText(): array;
    public function setMessage(string $message, string $key);
    public function getContent(array $response) :string;
    public function getModel() : string;
    public function makeImage(string $message) : array;
    public function getImageUrl(array $response) : string;
    public function getImageDescription(array $response) : string;
    public function getImageSize() : string;
}