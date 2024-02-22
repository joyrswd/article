<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dateTime = new \DateTime($this->resource['created_at']);
        $array = [
            'id' => $this->resource['id'],
            'title' => $this->resource['title'],
            'content' => $this->resource['content'],
            'llm_name' => $this->resource['llm_name'],
            'date' => $dateTime->format('Y-m-d'),
            "_links" => [
                'self' => [
                    'href' => route('post.show', ['post' => $this->resource['id']], false),
                ],
                'date' => [
                    'href' => route('date.index', ['date' => $dateTime->format('Y-m-d')], false),
                ]
            ],
        ];
        $embedded = [];
        if (array_key_exists('author', (array)$this->resource)) {
            $embedded['user'] = new AuthorResoruce($this->resource['author']);
        }
        if (empty($embedded) === false) {
            $array["_embedded"] = $embedded;
        }
        return $array;
    }
}
