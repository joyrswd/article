<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepLRepository
{
    private string $secret;
    private int $timeout;
    private string $endpoint;
    private string $model;

    public function __construct()
    {
        $config = config('llm.ai.deepl');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
    }

    /**
     * DeepLのAPIを使って翻訳を実行
     */
    public function requestApi($text, $lang): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'DeepL-Auth-Key ' . $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'text' => [$text],
                'target_lang' => $lang,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
        return empty($response) ? [] : $response->json();
    }

    /**
     * 結果から画像のURLを返す
     */
    public function getTranslation(array $response): string
    {
        return empty($response['translations']) ? '' : $response['translations'][0]['text'];
    }

    /**
     * モデル名を返す
     */
    public function getModel() : string
    {
        return $this->model;
    }

}
