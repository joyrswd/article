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
        $articles = [
            "data" => [
                0 => [
                    "id" => 2,
                    "path" => "/tmp/fakerAn2H9m",
                    "model_name" => "aperiam",
                ],
                1 => [
                    "id" => 3,
                    "path" => "/tmp/fakerBADloH",
                    "model_name" => "ut",
                ]
            ],
            "next_page_url" => "http://localhost?page=2"
        ];

        $resource = (new ImageResourceCollection($articles))->response()->getData(true);

        $this->assertEquals([
            'id' => 2,
            'model_name' => "aperiam",
            '_links' => ['self' => ['href' => "/tmp/fakerAn2H9m"]],
        ], $resource['data'][0]);
    }
}
