<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AiImageRepositoryInterface;

class StableDiffusionRepository extends ApiRepository implements AiImageRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(...config('llm.ai.stability'));
        $this->content['model'] = $this->model;
        $this->content['samples'] = 1;
        $this->content['text_prompts'] = [];
        $this->dataGetter = 'artifacts.0.base64';
    }

    public function setContent(mixed $text): void
    {
        $this->content['text_prompts'][] = ['text' => $text];
    }

    public function getImage(): string
    {
        $result = $this->requestApi();
        return empty($result) ? '' : base64_decode($result);
    }

}
