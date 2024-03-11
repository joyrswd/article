<?php

namespace Tests\Feature;

use App\Http\Resources\ArticleResource;

class ArticleResourceTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [
            'id' => 1,
            'title' => 'test',
            'content' => 'テスト',
            'created_at' => '2021-02-01',
            'llm_name' => 'model',
            'image' => ['path' => '/var/www/img/test.png']
        ];

        $resource = (new ArticleResource($articles))->response()->getData(true);

        $this->assertEquals(1, $resource['data']['id']);
        $this->assertEquals('test', $resource['data']['title']);
        $this->assertEquals('テスト', $resource['data']['content']);
        $this->assertEquals('model', $resource['data']['llm_name']);
        $this->assertEquals('2021-02-01', $resource['data']['date']);
        $this->assertEquals('/post/1', $resource['data']['_links']['self']['href']);
        $this->assertEquals('/date/2021-02-01', $resource['data']['_links']['date']['href']);
        $this->assertEquals('/var/www/img/test.png', $resource['data']['_links']['image']['href']);
    }
}
