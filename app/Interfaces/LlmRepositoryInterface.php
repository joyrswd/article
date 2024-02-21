<?php

declare(strict_types=1);

namespace App\Interfaces;

interface LlmRepositoryInterface
{
    public function excute(): array;
    public function setMessage(string $message, string $key);
    public function getContent(array $response) :string;
    public function getModel() : string;
}