<?php

namespace Tests\Feature;

use App\Repositories\OpenAiImageRepository;
use Illuminate\Support\Facades\Http;

class OpenAiImageRepositoryTest extends FeatureTestCase
{

    private OpenAiImageRepository $repository;

    public function setUp():void
    {
        parent::setUp();
        $this->repository = new OpenAiImageRepository();
    }

    /**
     * @test
     */
    public function makeImage_正常(): void
    {
        Http::fake();
        Http::shouldReceive('withHeaders')
            ->once()->andReturn(new class {
                public function timeout() {
                    return new class {
                        public function post () {
                            return new class {
                                public function json() {return [];}
                            };
                        }
                    };
                }
            });
        $result = $this->repository->makeImage(['画像生成']);
        $this->assertIsArray($result);
    }


    /**
     * @test
     */
    public function getModel_正常(): void
    {
        $this->setPrivateProperty('model', 'test', $this->repository);
        $result = $this->repository->getModel();
        $this->assertEquals('test', $result);
    }



}
