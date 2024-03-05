<?php

declare(strict_types=1);

namespace App\Repositories;

class DeepLRepository extends ApiRepository
{
    private $lang;
    public function __construct()
    {
        parent::__construct(...config('llm.ai.deepl'));
        $this->tokenType = 'DeepL-Auth-Key';
        $this->dataGetter = 'translations.0.text';
        $this->lang = app()->currentLocale();
    }

    protected function prepareContent(): array
    {
        return [
            'text' => $this->prompt,
            'target_lang' => strtoupper($this->lang)
        ];
    }

    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }

    protected function hasError(array $data): ?string
    {
        if (empty($data['translations'])) {
            return 'データの取得に失敗しました。';
        }
        return null;
    }

}
