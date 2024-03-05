<?php

declare(strict_types=1);

namespace App\Repositories;

class LlamaRepository extends ApiRepository
{
    public function __construct()
    {
        parent::__construct(...config('llm.ai.llama'));
        $this->tokenType = 'Bearer';
        $this->dataGetter = 'choices.0.message.content';
    }

    protected function prepareContent(): array
    {
        return [
            'model' => $this->model,
            'max_tokens' => 1000,
            'messages' => array_map(function($prompt){ return ["role" => 'user', "content" => $prompt];}, $this->prompt)
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
