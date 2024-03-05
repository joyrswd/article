<?php

declare(strict_types=1);

namespace App\Repositories;

class LlamaRepository extends ApiRepository
{
    public function __construct()
    {
        $config = config('llm.ai.llama');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
        
        $this->content = [
            'model' => $this->model,
            'messages' => [],
        ];
        $this->dataGetter = 'choices.0.message.content';
    }

    public function setContent(mixed $content): void
    {
        $this->content['messages'][] = ["role" => 'user', "content" => $content];
    }

    public function hasError($data) : ?string
    {
        if (array_key_exists('error', $data)) {
            return implode("\n", $data['error']);
        }
        return null;
    }

}
