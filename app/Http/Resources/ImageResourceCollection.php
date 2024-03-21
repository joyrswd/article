<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ImageResourceCollection extends ResourceCollection
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [];
        $rows = $this->resource['data']->resource;
        foreach ($rows as $row) {
            $resource[] = new ImageResource($row);
        }
        $next = $this->resource->get('next_page_url');
        return [
            'data' => $resource,
            '_links' => [
                'next' => ['href' => empty($next) ? '' : str_replace(url('/'), '', $next->resource)]
            ]
        ];
    }
}
