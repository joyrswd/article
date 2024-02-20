<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attribute;
use App\Traits\CrudRepositoryTrait;

class AttributeRepository
{
    use CrudRepositoryTrait;

    public function __construct(Attribute $model)
    {
        $this->model = $model;
        $this->relations = ['authors'];
    }

}
