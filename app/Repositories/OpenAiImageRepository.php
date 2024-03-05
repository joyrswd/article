<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AiImageRepositoryInterface;

class OpenAiImageRepository extends ApiRepository implements AiImageRepositoryInterface
{
    public function __construct()
    {
        $config = config('llm.ai.openai');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['image']['endpoint'];
        $this->model = $config['image']['model'];
        $this->tokenType = 'Bearer';
        $this->dataGetter = 'data.0.b64_json';
    }

    protected function prepareContent(): array
    {
        return [
            'model' => $this->model,
            'prompt' => implode("\n", $this->prompt),
            'n' => 1,
            'quality' => 'standard',
            'response_format' => 'b64_json',
            'size' => '1024x1024',
        ];
    }

    public function getImage(): string
    {
        $result = $this->requestApi();
        return empty($result) ? '' : base64_decode($result);
    }

}
