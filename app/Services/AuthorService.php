<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuthorRepository;

class AuthorService
{

    private AuthorRepository $repository;

    public function __construct(AuthorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {author}のバリデーションルール
     * @see \App\Providers\RouteServiceProvider::boot()
     */
    public function bind(mixed $value): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if (is_int($int) && $this->get($int)) {
            return $int;
        }
        abort(404, 'Not found.');
    }

    public function addOrFind(string $name, ?array $attributes = []) : array
    {
        $record = $this->repository->findOne(['name' => $name]);
        if (empty($record)) {
            $id = $this->repository->create([
                'name' => $name,
                'attributes' => $attributes,
            ]);
            $record = $this->repository->read($id);
        }
        return $record;
    }

    public function get(int $id)
    {
        return $this->repository->read($id);
    }

    public function find(array $params)
    {
        return $this->repository->find($params);
    }
}