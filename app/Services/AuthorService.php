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
}