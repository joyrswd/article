<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Image;
use App\Traits\CrudRepositoryTrait;

class ImageRepository
{
    use CrudRepositoryTrait;

    public function __construct(Image $model)
    {
        $this->model = $model;
        $this->relations = ['article'];
    }

}
