<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\LlmRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiRepository implements LlmRepositoryInterface
{
    private string $secret;
    private int $timeout;
    private string $textEndpoint;
    private string $textModel;
    private string $imageEndpoint;
    private string $imageModel;
    private string $imageSize;
    private $roles = [
        'system' => 'system',
        'user' => 'user',
    ];

    private array $messages = [];

    public function __construct()
    {
        $config = config('llm.ai.openai');
        $this->secret = $config['secret'];
        $this->timeout = $config['timeout'];
        $this->textEndpoint = $config['text']['endpoint'];
        $this->textModel = $config['text']['model'];
        $this->imageEndpoint = $config['image']['endpoint'];
        $this->imageModel = $config['image']['model'];
        $this->imageSize = $config['image']['size'];
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function makeText(): array
    {
        if (empty($this->messages)) {
            return [];
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->secret
            ])->timeout($this->timeout)->post($this->textEndpoint, [
                'model' => $this->textModel,
                'messages' => $this->messages,
                'presence_penalty' => 1,
                'top_p' => 0,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
        $this->messages = [];
        return empty($response) ? [] : $response->json();
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
            ])->timeout($this->timeout)->post($this->imageEndpoint, [
                'model' => $this->imageModel,
                'prompt' => $prompt,
                'n' => 1,
                'size' => $this->imageSize,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
        return empty($response) ? [] : $response->json();
    }

    /**
     * メッセージを設定する
     */
    public function setMessage(string $message, string $key)
    {
        $role = $this->roles[$key]??$this->roles['system'];
        $this->messages[] = [
            "role" => $role,
            "content" => $message,
        ];
    }

    /**
     * 結果から内容を返す
     */
    public function getContent(array $response) :string
    {
        return $response['choices'][0]['message']['content'];
    }

    /**
     * モデル名を返す
     */
    public function getModel(?string $type = '') : string
    {
        return match($type) {
            'text' => $this->textModel,
            'image' => $this->imageModel,
            default => $this->textModel
        };
    }
}
