<?php

declare(strict_types=1);

namespace App\Repositories;

class ClaudeRepository extends ApiRepository
{
    public function __construct()
    {
        $config = config('llm.ai.claude');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
        $this->header['x-api-key'] = $this->secret;
        $this->header['anthropic-version'] = '2023-06-01';
        $this->tokenType = '';

        $this->content = [
            'model' => $this->model,
            'messages' => [],
            'max_tokens' => 1000,
        ];
        $this->dataGetter = 'content.0.text';
    }

    public function setContent(mixed $content): void
    {
        $text = $this->content['messages'][0]['content']??'';
        $this->content['messages'] = [["role" => 'user', "content" =>  $text . "\n" .  $content]];
    }

    public function hasError($data) : ?string
    {
        if (array_key_exists('error', $data)) {
            return implode("\n", $data['error']);
        }
        return null;
    }

}
