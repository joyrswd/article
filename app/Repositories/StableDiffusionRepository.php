<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AiImageRepositoryInterface;

class StableDiffusionRepository extends ApiRepository implements AiImageRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(...config('llm.ai.stability'));
        $this->dataGetter = 'artifacts.0.base64';
        $this->tokenType = 'Bearer';
    }

    protected function prepareContent(): array
    {
        return [
            'model' => $this->model,
            'samples' => 1,
            'text_prompts' => array_map(function($prompt){ return ['text' => $prompt];}, $this->prompt),
        ];
    }

    public function getImage(): string
    {
        $result = $this->requestApi();
        return empty($result) ? '' : base64_decode($result);
    }

}
