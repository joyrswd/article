<?php

declare(strict_types=1);

namespace App\Repositories;

class GoogleAiRepository extends ApiRepository
{

    public function __construct()
    {
        parent::__construct(...config('llm.ai.google'));
        $this->header['x-goog-api-key'] = $this->secret;
        $this->dataGetter = 'candidates.0.content.parts.0.text';
    }

    protected function prepareContent(): array
    {
        return [
            'contents' => [
                'role' => 'user',
                'parts' => array_map(function($prompt){ return ['text' => $prompt];}, $this->prompt)
            ]
        ];
    }

    protected function hasError(array $data): ?string
    {
        if (array_key_exists('error', $data)) {
            return print_r($data['error'], true);
        } elseif (empty(data_get($data, 'candidates.0.content'))) {
            return print_r($data['candidates'][0], true);
        }
        return null;
    }

}
