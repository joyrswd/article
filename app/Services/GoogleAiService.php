<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Repositories\GoogleAiRepository;

class GoogleAiService implements LlmServiceInterface
{
    use LlmServiceTrait;

    public function __construct(GoogleAiRepository $repository)
    {
        $this->repository = $repository;
        $this->conditions = config('llm.condition');
    }

    public function makeImage(string $article): array
    {
        return [];
    }

}
