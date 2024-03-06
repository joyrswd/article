<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ApiRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

abstract class ApiRepository implements ApiRepositoryInterface
{
    protected string $secret;
    protected int $timeout;
    protected string $endpoint;
    protected string $model;
    protected array $header = ['Content-Type' => 'application/json'];
    protected array $prompt = [];
    protected string $tokenType = '';
    protected string $dataGetter = '';
    protected string $method = 'post';

    public function __construct(string $secret, int $timeout, string $endpoint, string $model)
    {
        $this->secret = $secret;
        $this->timeout = $timeout;
        $this->endpoint = $endpoint;
        $this->model = $model;
    }

    abstract protected function prepareContent(): array;

    public function requestApi(): mixed
    {
        try {
            $content = $this->prepareContent();
            $request = Http::withHeaders($this->header);
            $request->timeout($this->timeout);
            if (empty($this->tokenType) === false) {
                $request->withToken($this->secret, $this->tokenType);
            }
            //リクエスト実行
            $response = $request->{$this->method}($this->endpoint, $content);
            //エラーがあれば例外をスロー
            $response->throw();
            //データ取得
            $data = $response->json();
            if ($message = $this->hasError($data)) {
                $output = $this->endpoint . "\n" . $message;
                throw new Exception($output);
            }
            //promptを空にする
            $this->prompt = [];
            return $this->getData($data);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function addPrompt(mixed $content): void
    {
        $this->prompt[] = $content;
    }

    protected function getData(array $data): mixed
    {
        return data_get($data, $this->dataGetter);
    }

    /**
     * エラー判定
     */
    protected function hasError(array $response): ?string
    {
        return null;
    }

    /**
     * モデル名を返す
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
