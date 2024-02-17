<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OpenAiRoleEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiRepository
{
    private string $secret;
    private string $endpoint;
    private string $model;
    private int $timeout;

    private array $messages = [];

    public function __construct(string $secret, string $endpoint, string $model, ?int $timeout = 60)
    {
        $this->secret = $secret;
        $this->endpoint = $endpoint;
        $this->model = $model;
        $this->timeout = $timeout;
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function excute(): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->secret
            ])->timeout($this->timeout)->post($this->endpoint, [
                'model' => $this->model,
                'messages' => $this->messages,
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
    public function setMessage(string $message, ?OpenAiRoleEnum $role=OpenAiRoleEnum::User)
    {
        $this->messages[] = [
            "role" => $role,
            "content" => $message,
        ];
    }
}
