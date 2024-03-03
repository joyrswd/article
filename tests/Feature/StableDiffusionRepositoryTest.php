<?php

namespace Tests\Feature;

use App\Repositories\StableDiffusionRepository;
use Illuminate\Support\Facades\Http;

class StableDiffusionRepositoryTest extends FeatureTestCase
{

    private StableDiffusionRepository $repository;

    public function setUp():void
    {
        parent::setUp();
        $this->repository = new StableDiffusionRepository();
    }

    /**
     * @test
     */
    public function setContent_正常(): void
    {
        $this->repository->setContent('テスト');
        $content = $this->getPrivateProperty('content', $this->repository);
        $this->assertEquals([['text' => 'テスト']], $content['text_prompts']);
    }

    /**
     * @test
     */
    public function requestApi_正常(): void
    {
        Http::fake();
        Http::shouldReceive('withHeaders')
            ->once()->andReturn(new class {
                public function timeout() {}
                public function withToken() {}
                public function post () {
                    return new class {
                        public function json() {return [
                            'artifacts' => [['base64'=>'画像データ']]
                        ];}
                    };
                }
            });
        $this->repository->setContent('画像生成');
        $result = $this->repository->requestApi();
        $this->assertEquals('画像データ', $result);
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

    /**
     * @test
     */
    public function getImage_正常(): void
    {
        $repository = mock(StableDiffusionRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->once()->andReturn(base64_encode('あああああ'));
        $result = $repository->getImage();
        $this->assertEquals('あああああ', $result);
    }


}
