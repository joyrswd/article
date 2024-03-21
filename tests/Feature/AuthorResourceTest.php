<?php

namespace Tests\Feature;

use App\Http\Resources\AuthorResource;

class AuthorResourceTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [
            'id' => 1,
            'name' => 'test',
            'articles' => [[
                'id' => 1,
                'title' => 'test',
                'content' => 'テスト',
                'created_at' => '2021-02-01',
                'llm_name' => 'model',
                'image' => ['path' => '/var/www/img/test.png']
                ]
            ],
            'attributes' => [[
                'id' => 1,
                'name' => 'test',
                'type' => 'テスト',
                ]
            ]
        ];

        $resource = (new AuthorResource($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 1,
            'name' => 'test',
            '_links' => ['self' => ['href' => '/user/1']],
            '_embedded' => [
                'posts' => [[
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
                    ]],
                'attrs' => [[
                    'id' => 1,
                    'name' => 'test',
                    'type' => 'テスト',    
                    '_links' => [
                        'self' => ['href' => '/attr/1'],
                        ]
                ]]
            ]
        ], $resource['data']);
    }
}
