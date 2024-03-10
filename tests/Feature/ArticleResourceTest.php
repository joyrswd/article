<?php

namespace Tests\Feature;

use App\Http\Resources\ArticleResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{

    public function test_product_resource()
    {
        $articles = [
            'id' => 1,
            'title' => 'test',
            'content' => 'テスト',
        ];

        $resource = (new ArticleResource($articles))->response()->getData(true);

        $this->assertEquals($product->id, $resource['data']['id']);
        $this->assertEquals($product->name, $resource['data']['name']);
        // 他のフィールドも同様にテスト...
    }
}
