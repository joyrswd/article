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
        $this->content = [
            'model' => $this->model,
            'prompt' => '',
            'n' => 1,
            'quality' => 'standard',
            'response_format' => 'b64_json',
            'size' => '1024x1024',
        ];
        $this->dataGetter = 'data.0.b64_json';
    }

    public function setContent(mixed $content): void
    {
        $this->content['prompt'] .= $content;
    }

    public function getImage(): string
    {
        $result = $this->requestApi();
        return empty($result) ? '' : base64_decode($result);
    }

}
