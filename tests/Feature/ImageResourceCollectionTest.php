<?php

namespace Tests\Feature;

use App\Http\Resources\ImageResourceCollection;

class ImageResourceCollectionTest extends FeatureTestCase
{

    /**
     * @test
     */
    public function 正常()
    {
        $articles = [[
            'id' => 1,
            'model_name' => 'test',
            'path' => public_path() . '/img/test.png', 
        ]];

        $resource = (new ImageResourceCollection($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 1,
            'model_name' => 'test',
            '_links' => ['self' => ['href' => '/img/test.png']],
        ], $resource['data'][0]);
    }
}
