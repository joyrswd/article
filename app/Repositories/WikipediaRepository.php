<?php

declare(strict_types=1);

namespace App\Repositories;

class WikipediaRepository extends ApiRepository
{
    public function __construct()
    {
        $lang = app()->currentLocale();
        $this->endpoint = "https://{$lang}.wikipedia.org/w/api.php";
        $this->timeout = 10;
        $this->tokenType = '';
        $this->method = 'get';
        $this->content = [
            'format' => 'json',
            'action' => 'query',
            'prop' => 'extracts',
            'explaintext' => 'explaintext',
            'redirects' => 1,
            'titles' => '',
        ];
        $this->dataGetter = 'query.pages';
    }

    public function setContent(mixed $content): void
    {
        $this->content['titles'] = $content;
    }

    protected function getData(array $data): mixed
    {
        $array = parent::getData($data);
        if(empty($array)) {
            return '';
        }
        $page = current($array);
        $contents = array_filter(explode("\n", $page['extract']));
        return implode("\n", array_slice($contents, 1));
    }

}
