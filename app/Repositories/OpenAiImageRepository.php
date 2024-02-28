<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AiImageRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiImageRepository implements AiImageRepositoryInterface
{
    private string $secret;
    private int $timeout;
    private string $endpoint;
    private string $model;
    private string $size;

    public function __construct()
    {
        $config = config('llm.ai.openai');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['image']['endpoint'];
        $this->model = $config['image']['model'];
        $this->size = $config['image']['size'];
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function makeImage($prompt): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'model' => $this->model,
                'prompt' => $prompt,
                'n' => 1,
                'size' => $this->size,
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
    public function getUrl(array $response): string
    {
        return empty($response['data']) ? '' : $response['data'][0]['url'];
    }

    /**
     * 結果から画像の説明文を返す
     */
    public function getDescription(array $response): string
    {
        return empty($response['data']) ? '' : $response['data'][0]['revised_prompt'];
    }

    /**
     * モデル名を返す
     */
    public function getModel() : string
    {
        return $this->model;
    }

    /**
     * イメージサイズを返す
     */
    public function getSize(): string
    {
        return $this->size;
    }

}
