<?php

namespace Tests\Feature;

use App\Http\Resources\ImageResource;

class ImageResourceTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [
            'id' => 1,
            'model_name' => 'test',
            'path' => public_path() . '/img/test.png', 
            'articles' => [[
                'id' => 1,
                'title' => 'test',
                'content' => 'テスト',
                'created_at' => '2021-02-01',
                'llm_name' => 'model',
                ]
            ],
        ];

        $resource = (new ImageResource($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 1,
            'model_name' => 'test',
            '_links' => [
                'src' => ['href' => '/img/test.png'],
                'self' => ['href' => '/image/1']
            ],
            '_embedded' => [
                'posts' => [[
                    'id' => 1,
                    'title' => 'test',
                    'content' => 'テスト',
                    'date' => '2021-02-01',
                    'llm_name' => 'model',
                    '_links' => [
                        'self' => ['href' => '/post/1'],
                        'image' => ['href' => ''],
                        'date' => ['href' => '/date/2021-02-01']
                        ]
                    ]],
            ]
        ], $resource['data']);
    }
}
