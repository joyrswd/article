<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Interfaces\AiImageServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Repositories\OpenAiRepository;
use App\Repositories\OpenAiImageRepository;

class OpenAiService implements LlmServiceInterface, AiImageServiceInterface
{
    use LlmServiceTrait;

    private OpenAiImageRepository $imageRepository;

    public function __construct(OpenAiRepository $repository, OpenAiImageRepository $imageRepository)
    {
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->conditions = config('llm.condition');
    }

    public function makeImage(string $article) : string
    {
        $prompt = "次の文章の挿絵を生成してください。\n\n" . $article;
        $this->imageRepository->setContent($prompt);
        $response = $this->imageRepository->getImage();
        if (empty($response)) {
            return '';
        }
        return $response;
    }

    public function getImageModel() : string
    {
        return $this->imageRepository->getModel();
    }


}
