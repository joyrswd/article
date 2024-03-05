<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Repositories\LlamaRepository;

class LlamaService implements LlmServiceInterface
{
    use LlmServiceTrait;

    public function __construct(LlamaRepository $repository)
    {
        $this->repository = $repository;
        $this->conditions = config('llm.condition');
    }

}
