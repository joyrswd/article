<?php

namespace Tests\Feature;

use App\Http\Resources\AuthorResourceCollection;

class AuthorResourceCollectionTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [[
            'id' => 1,
            'name' => 'test',
        ]];

        $resource = (new AuthorResourceCollection($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 1,
            'name' => 'test',
            '_links' => ['self' => ['href' => '/user/1']],
        ], $resource['data'][0]);
    }
}
