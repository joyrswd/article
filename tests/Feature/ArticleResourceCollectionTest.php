<?php

namespace Tests\Feature;

use App\Http\Resources\ArticleResourceCollection;

class ArticleResourceCollectionTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [
            [
                'id' => 1,
                'title' => 'test',
                'content' => 'テスト',
                'created_at' => '2021-02-01',
                'llm_name' => 'model',
                'image' => ['path' => '/var/www/img/test.png']
            ],
            [
                'id' => 2,
                'title' => 'test2',
                'content' => 'テスト2',
                'created_at' => '2021-02-02',
                'llm_name' => 'model2',
                'image' => ['path' => '/var/www/img/test2.png']
            ],
        ];

        $resource = (new ArticleResourceCollection($articles))->response()->getData(true);

        $this->assertEquals(2, $resource['data'][1]['id']);
        $this->assertEquals('test2', $resource['data'][1]['title']);
        $this->assertEquals('テスト2', $resource['data'][1]['content']);
        $this->assertEquals('model2', $resource['data'][1]['llm_name']);
        $this->assertEquals('2021-02-02', $resource['data'][1]['date']);
        $this->assertEquals('/post/2', $resource['data'][1]['_links']['self']['href']);
        $this->assertEquals('/date/2021-02-02', $resource['data'][1]['_links']['date']['href']);
        $this->assertEquals('/var/www/img/test2.png', $resource['data'][1]['_links']['image']['href']);
    }
}
