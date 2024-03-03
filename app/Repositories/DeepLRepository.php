<?php

declare(strict_types=1);

namespace App\Repositories;

class DeepLRepository extends ApiRepository
{
    public function __construct()
    {
        parent::__construct(...config('llm.ai.deepl'));
        $this->tokenType = 'DeepL-Auth-Key';
        $this->dataGetter = 'translations.0.text';
        $this->content['text'] = [];
        $this->content['target_lang'] = app()->currentLocale();
    }

    public function setContent(mixed $text):void
    {
        $this->content['text'][] = $text;
    }

    public function setLang(string $lang)
    {
        $this->content['target_lang'] = strtoupper($lang);
    }

    protected function hasError(array $data): ?string
    {
        if (empty($data['translations'])) {
            return 'データの取得に失敗しました。';
        }
        return null;
    }

}
