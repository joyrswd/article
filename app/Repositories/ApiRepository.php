<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ApiRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class ApiRepository implements ApiRepositoryInterface
{
    protected string $secret;
    protected int $timeout;
    protected string $endpoint;
    protected string $model;
    protected array $header = ['Content-Type' => 'application/json'];
    protected array $content = [];
    protected string $tokenType = 'Bearer';
    protected string $dataGetter = '';

    public function __construct(string $secret, int $timeout, string $endpoint, string $model)
    {
        $this->secret = $secret;
        $this->timeout = $timeout;
        $this->endpoint = $endpoint;
        $this->model = $model;
    }

    public function requestApi(): mixed
    {
        try {
            $request = Http::withHeaders($this->header);
            $request->timeout($this->timeout);
            if (empty($this->tokenType) === false) {
                $request->withToken($this->secret, $this->tokenType);
            }
            $response = $request->post($this->endpoint, $this->content);
            $data = $response->json();
            if ($message = $this->hasError($data)) {
                throw new \Exception($message);
            }
            return data_get($data, $this->dataGetter);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
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
