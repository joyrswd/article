<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Interfaces\AiImageServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Traits\StableDiffusionTrait;
use App\Repositories\GoogleAiRepository;
use App\Repositories\StableDiffusionRepository;

class GoogleAiService implements LlmServiceInterface, AiImageServiceInterface
{
    use LlmServiceTrait, StableDiffusionTrait;

    public function __construct(GoogleAiRepository $repository, StableDiffusionRepository $imageRepository)
    {
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->conditions = config('llm.condition');
    }

}
