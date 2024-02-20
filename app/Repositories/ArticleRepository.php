<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Traits\CrudRepositoryTrait;

class ArticleRepository
{
    use CrudRepositoryTrait;

    public function __construct(Article $model)
    {
        $this->model = $model;
        $this->relations = ['author'];
    }

}
