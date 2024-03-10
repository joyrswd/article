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
        $this->method = 'get';
        $this->dataGetter = 'query.pages';
    }

    protected function prepareContent(): array
    {
        return [
            'format' => 'json',
            'action' => 'query',
            'prop' => 'extracts',
            'explaintext' => 1,
            'exlimit' => 1,
            'formatversion' => 2,
            'redirects' => 1,
            'titles' => implode(' ', $this->prompt),
        ];
    }

    protected function getData(array $data): mixed
    {
        $array = parent::getData($data);
        if (empty($array)) {
            return '';
        }
        $page = current($array);
        $rows = array_values(array_filter(explode("\n", $page['extract'])));
        return $this->parse($rows);
    }

    /**
     * 再帰的にWilipediaテキストをパースして連想配列を生成する
     */
    private function parse(array $lines, int $level=1): array
    {
        $rows = [];
        while((list($line, $index) = [current($lines), key($lines)]) && $line) {
            if(preg_match('/\=+\s([^\=\s]+)\s(\=+)/', $line, $matches)) {
                $key = $matches[1];
                $newLevel = strlen($matches[2]);
                if ($level < $newLevel) {
                    $rows[$key] = $this->parse(array_slice($lines, $index+1), $newLevel);
                    $length = collect($rows[$key])->flatten()->count() + collect($rows[$key])->keys()->filter(function($v){return is_string($v);})->count();
                    for($i=0; $i<$length; $i++) next($lines);
                } elseif ($level >= $newLevel) {
                    break;
                }
            } else {
                $rows[] = $line;
            }
            next($lines);
        }
        return $rows;
    }
}
