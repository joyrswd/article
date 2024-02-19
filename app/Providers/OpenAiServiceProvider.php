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
            $params = config('llm.ai.openai', []);
            $repository = new OpenAiRepository(...$params);
            $conditions = config('llm.condition', []);
            return new OpenAiService($repository, $conditions);
        });
    }

}
