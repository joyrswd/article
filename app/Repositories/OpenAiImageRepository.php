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

    public function __construct()
    {
        $config = config('llm.ai.openai');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['image']['endpoint'];
        $this->model = $config['image']['model'];
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function makeImage(array $messages): array
    {
        $prompt = implode("\n\n", $messages);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'model' => $this->model,
                'prompt' => $prompt,
                'n' => 1,
                'quality' => 'standard',
                'response_format' => 'b64_json',
                'size' => '1024x1024',
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
    public function getBinary(array $response): string
    {
        return empty($response['data']) ? '' : base64_decode($response['data'][0]['b64_json']);
    }

    /**
     * モデル名を返す
     */
    public function getModel() : string
    {
        return $this->model;
    }

}
