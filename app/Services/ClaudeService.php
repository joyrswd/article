<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Interfaces\AiImageServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Traits\StableDiffusionTrait;
use App\Repositories\ClaudeRepository;
use App\Repositories\StableDiffusionRepository;

class ClaudeService implements LlmServiceInterface, AiImageServiceInterface
{
    use LlmServiceTrait, StableDiffusionTrait;

    public function __construct(ClaudeRepository $repository, StableDiffusionRepository $imageRepository)
    {
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->conditions = config('llm.condition');
    }

}
