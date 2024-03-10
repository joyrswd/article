<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'name' => __($this->resource['name']),
            'type' => $this->resource['type'],
            "_links" => [
                'self' => [
                    'href' => route('attr.index', ['attr' => $this->resource['id']], false),
                ],
            ],
        ];
        $embedded = [];
        if (array_key_exists('articles', (array)$this->resource)) {
            $embedded['posts'] = new ArticleResourceCollection($this->resource['articles']);
        }
        if (empty($embedded) === false) {
            $array["_embedded"] = $embedded;
        }
        return $array;
    }
}
