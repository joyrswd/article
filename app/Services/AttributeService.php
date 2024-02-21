<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AttributeRepository;

class AttributeService
{

    private AttributeRepository $repository;

    public function __construct(AttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function addOrFind(array $params) : array
    {
        $records = [];
        foreach ($params as $key => $value) {
            $record = $this->repository->findOne(['name' => $value]);
            if (empty($record)) {
                $id = $this->repository->create(['name' => $value, 'type' => $key]);
                $record = $this->repository->read($id);
            }
            $records[] = $record;
        }
        return $records;
    }
}