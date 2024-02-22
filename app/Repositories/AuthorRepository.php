<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Author;
use App\Traits\CrudRepositoryTrait;

class AuthorRepository
{
    use CrudRepositoryTrait;

    public function __construct(Author $model)
    {
        $this->model = $model;
        $this->relations = ['attributes', 'articles'];
    }

}
