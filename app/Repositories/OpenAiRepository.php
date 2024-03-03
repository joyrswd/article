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
        $this->content = [
            'model' => $this->model,
            'presence_penalty' => 1,
            'top_p' => 0,
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
