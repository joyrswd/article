<?php

namespace Tests\Feature;

use App\Http\Resources\AttributeResourceCollection;

class AttributeResourceCollectionTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [[
            'id' => 1,
            'name' => 'test',
            'type' => 'テスト',
        ]];

        $resource = (new AttributeResourceCollection($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 1,
            'name' => 'test',
            'type' => 'テスト',
            '_links' => ['self' => ['href' => '/attr/1']],
        ], $resource['data'][0]);
    }
}
