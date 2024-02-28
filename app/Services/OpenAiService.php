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

    public function makeImage(string $article) : array
    {
        $prompt = "次の文章の挿絵を生成してください。挿絵に文字は使用しないでください。\n\n" . $article;
        $response = $this->repository->makeImage($prompt);
        if (empty($response)) {
            return [];
        }
        $url = $this->repository->getImageUrl($response);
        $description = $this->repository->getImageDescription($response);
        $model = $this->repository->getModel('image');
        $size = $this->repository->getImageSize();
        return compact('url', 'description', 'size', 'model');
    }
}
