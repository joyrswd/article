<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\LlmRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiRepository implements LlmRepositoryInterface
{
    private string $secret;
    private string $endpoint;
    private string $model;
    private int $timeout;

    private array $messages = [];

    public function __construct()
    {
        $config = config('llm.ai.openai');
        $this->secret = $config['secret'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
        $this->timeout = $config['timeout'];
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function excute(): array
    {
        if (empty($this->messages)) {
            return [];
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'model' => $this->model,
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
     * メッセージを設定する
     */
    public function setMessage(string $message, ?string $role=null)
    {
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
    public function getModel() : string
    {
        return $this->model;
    }
}
