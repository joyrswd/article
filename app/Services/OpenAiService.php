<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Repositories\OpenAiRepository;

class OpenAiService implements LlmServiceInterface
{
    use LlmServiceTrait;

    public function __construct(OpenAiRepository $repository)
    {
        $this->repository = $repository;
        $this->conditions = config('llm.condition');
    }

}
