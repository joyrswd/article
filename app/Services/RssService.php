<?php

declare(strict_types=1);

namespace App\Services;

class RssService 
{
    private function getFilePath() : string
    {
        $root = public_path();
        $dir = $root . '/rss';
        if (is_dir($dir) === false) {
            mkdir($dir);
        }
        $path = $dir . '/' . app()->currentLocale() . '.xml';
        if (file_exists($path) === false) {
            touch($path);
        }
        return $path;
    }

    public function fetchRss(array $items) : void
    {
        $path = $this->getFilePath();
        $atomLink = __('site.url') . str_replace(public_path() . '/', '', $path);
        $content = view('rss', ['atomLink' => $atomLink, 'items' => $items])->render();
        file_put_contents($path, $content);
    }
}