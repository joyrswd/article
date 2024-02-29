<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LlmServiceInterface;
use App\Traits\LlmServiceTrait;
use App\Repositories\OpenAiRepository;
use App\Repositories\OpenAiImageRepository;

class OpenAiService implements LlmServiceInterface
{
    use LlmServiceTrait;

    private OpenAiImageRepository $imageRepository;

    public function __construct(OpenAiRepository $repository, OpenAiImageRepository $imageRepository)
    {
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->conditions = config('llm.condition');
    }

    public function makeImage(string $article) : array
    {
        $prompt = "次の文章の挿絵を生成してください。挿絵に文字は使用しないでください。\n\n" . $article;
        $response = $this->imageRepository->makeImage($prompt);
        if (empty($response)) {
            return [];
        }
        $url = $this->imageRepository->getUrl($response);
        $description = $this->imageRepository->getDescription($response);
        $model = $this->imageRepository->getModel();
        $size = $this->imageRepository->getSize();
        return compact('url', 'description', 'size', 'model');
    }
}
