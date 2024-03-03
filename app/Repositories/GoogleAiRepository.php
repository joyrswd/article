<?php

declare(strict_types=1);

namespace App\Repositories;

class GoogleAiRepository extends ApiRepository
{

    public function __construct()
    {
        parent::__construct(...config('llm.ai.google'));
        $this->header['x-goog-api-key'] = $this->secret;
        $this->tokenType = '';
        $this->content['contents'] = [
            'role' => 'user',
            'parts' => []
        ];
        $this->dataGetter = 'candidates.0.content.parts.0.text';
    }

    public function setContent(mixed $text):void
    {
        $this->content['contents']['parts'][] = ['text' => $text];
    }

    protected function hasError(array $data): ?string
    {
        if (array_key_exists('error', $data)) {
            return implode("\n", $data['error']);
        }
        return null;
    }

}
