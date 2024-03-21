<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'model_name' => $this->resource['model_name'],
            "_links" => [
                'self' => [
                    'href' => route('image.show', ['image' => $this->resource['id']], false),
                ],
                'src' => [
                    'href' => empty($this->resource['path']) ? '' : str_replace(public_path(), '', $this->resource['path']),
                ],
            ],
        ];
        $embedded = [];
        if (array_key_exists('article', (array)$this->resource)) {
            $embedded['post'] = new ArticleResource($this->resource['article']);
        }
        if (empty($embedded) === false) {
            $array["_embedded"] = $embedded;
        }
        return $array;
    }
}
