<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\LlmRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleAiRepository implements LlmRepositoryInterface
{
    private string $secret;
    private string $endpoint;
    private string $model;
    private int $timeout;

    private array $messages = [];

    public function __construct()
    {
        $config = config('llm.ai.google');
        $this->secret = $config['secret'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
        $this->timeout = $config['timeout'];
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
                'x-goog-api-key' => $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'contents' => [
                    "role" => 'user',
                    "parts" => [$this->messages],
                ]
            ]);
            $data = $response->json();
            if (array_key_exists('error', $data)) {
                throw new Exception(implode("\n", $data['error']));
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return [];
        }
        $this->messages = [];
        if (empty($data['candidates']) ) {
            Log::error(print_r($data, true));
            return [];
        }
        return $data['candidates'];
    }

    /**
     * メッセージを設定する
     */
    public function setMessage(string $message, string $key)
    {
        $this->messages[] = ['text' => $message];
    }

    /**
     * 結果から内容を返す
     */
    public function getContent(array $response) :string
    {
        return empty($response) ? '' : $response[0]['content']['parts'][0]['text'];
    }

    /**
     * モデル名を返す
     */
    public function getModel() : string
    {
        return $this->model;
    }
}
