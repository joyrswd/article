<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if(empty($this->resource)) {
            return [];
        }
        $array = [
            'id' => $this->resource['id'],
            'name' => $this->resource['name'],
            "_links" => [
                'self' => [
                    'href' => route('user.show', ['user' => $this->resource['id']], false),
                ],
            ],
        ];
        $embedded = [];
        if (array_key_exists('articles', (array)$this->resource) && empty($this->resource['articles']) === false) {
            $embedded['posts'] = new ArticleResoruceCollection($this->resource['articles']);
        }
        if (array_key_exists('attributes', (array)$this->resource) && empty($this->resource['attributes']) === false) {
            $embedded['attrs'] = new AttributeResoruceCollection($this->resource['attributes']);
        }
        if (empty($embedded) === false) {
            $array["_embedded"] = $embedded;
        }
        return $array;
    }
}
