<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OpenAiService;
use App\Repositories\OpenAiRepository;
use Exception;

class OpenAiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(OpenAiService::class, function ($app) {
            $params = $this->getConfig('llm.openai');
            $repository = new OpenAiRepository(...$params);
            return new OpenAiService($repository);
        });
    }

    private function getConfig($key): array
    {
        $config = config($key);
        if (array_keys($config) !== ['params']) {
            throw new Exception('コンフィグファイルの設定に不備があります。');
        } elseif (is_array($config['params']) === false) {
            throw new Exception('paramsの設定方法が不正です。');
        }
        return $config['params'];
    }

}
