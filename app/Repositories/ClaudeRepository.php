<?php

declare(strict_types=1);

namespace App\Repositories;

class ClaudeRepository extends ApiRepository
{
    public function __construct()
    {
        parent::__construct(...config('llm.ai.claude'));
        $this->header['x-api-key'] = $this->secret;
        $this->header['anthropic-version'] = '2023-06-01';
        $this->dataGetter = 'content.0.text';
    }

    protected function prepareContent(): array
    {
        return [
            'model' => $this->model,
            'max_tokens' => 1000,
            'messages' => [['role' => 'user', 'content' => implode("\n", $this->prompt)]],
        ];
    }

    public function hasError($data): ?string
    {
        if (array_key_exists('error', $data)) {
            return implode("\n", $data['error']);
        }
        return null;
    }
}
