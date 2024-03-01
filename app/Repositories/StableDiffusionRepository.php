<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AiImageRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StableDiffusionRepository implements AiImageRepositoryInterface
{
    private string $secret;
    private int $timeout;
    private string $endpoint;
    private string $model;

    public function __construct()
    {
        $config = config('llm.ai.stability');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function makeImage(array $messages): array
    {
        $prompt = array_map(function($message){return ['text' => $message];}, $messages);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'model' => $this->model,
                'text_prompts' => $prompt,
                "samples"=> 1,
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
        return empty($response['artifacts']) ? '' : base64_decode($response['artifacts'][0]['base64']);
    }

    /**
     * モデル名を返す
     */
    public function getModel() : string
    {
        return $this->model;
    }

}
