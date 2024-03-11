<?php

namespace Tests\Feature;

use App\Http\Resources\AttributeResource;

class AttributeResourceTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [
            'id' => 1,
            'name' => 'test',
            'type' => 'テスト',
            'articles' => [[
                'id' => 1,
                'title' => 'test',
                'content' => 'テスト',
                'created_at' => '2021-02-01',
                'llm_name' => 'model',
                'image' => ['path' => '/var/www/img/test.png']
                ]
            ]
        ];

        $resource = (new AttributeResource($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 1,
            'name' => 'test',
            'type' => 'テスト',
            '_links' => ['self' => ['href' => '/attr/1']],
            '_embedded' => ['posts' => [[
                'id' => 1,
                'title' => 'test',
                'content' => 'テスト',
                'date' => '2021-02-01',
                'llm_name' => 'model',
                '_links' => [
                    'image' => ['href' => '/var/www/img/test.png'],
                    'self' => ['href' => '/post/1'],
                    'date' => ['href' => '/date/2021-02-01']
                    ]
                ]
            ]]
        ], $resource['data']);
    }
}
