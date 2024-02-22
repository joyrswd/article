<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleResoruceCollection extends ResourceCollection
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            '_embedded' => [
                'posts' => $this->resource->map(function ($row) {
                    return new ArticleResoruce($row);
                })->all()
            ]
        ];
    }
}