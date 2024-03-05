<?php

declare(strict_types=1);

namespace App\Repositories;

class OpenAiRepository extends ApiRepository
{
    public function __construct()
    {
        $config = config('llm.ai.openai');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['text']['endpoint'];
        $this->model = $config['text']['model'];
        $this->tokenType = 'Bearer';
        $this->dataGetter = 'choices.0.message.content';
    }

    protected function prepareContent(): array
    {
        return [
            'model' => $this->model,
            'presence_penalty' => 1,
            'top_p' => 0,
            'messages' => array_map(function($prompt){ return ["role" => 'user', "content" => $prompt];}, $this->prompt),
        ];
    }

    public function hasError($data) : ?string
    {
        if (array_key_exists('error', $data)) {
            return implode("\n", $data['error']);
        }
        return null;
    }

}
