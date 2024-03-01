<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Interfaces\AiImageServiceInterface;
use App\Interfaces\AiImageRepositoryInterface;
use App\Traits\LlmServiceTrait;
use App\Repositories\OpenAiRepository;
use App\Repositories\OpenAiImageRepository;

class OpenAiService implements LlmServiceInterface, AiImageServiceInterface
{
    use LlmServiceTrait;

    private AiImageRepositoryInterface $imageRepository;

    public function __construct(OpenAiRepository $repository, OpenAiImageRepository $imageRepository)
    {
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->conditions = config('llm.condition');
    }

    public function makeImage(string $article) : string
    {
        $prompt = ["次の文章の挿絵を生成してください。", $article];
        $response = $this->imageRepository->makeImage($prompt);
        if (empty($response)) {
            return '';
        }
        return $this->imageRepository->getBinary($response);
    }

    public function getImageModel() : string
    {
        return $this->imageRepository->getModel();
    }


}
