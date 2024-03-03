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
    public function setContent_正常(): void
    {
        $this->repository->setContent('テスト');
        $content = $this->getPrivateProperty('content', $this->repository);
        $this->assertEquals('テスト', $content['prompt']);
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
                            'data' => [['b64_json'=>'画像データ']]
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
        $repository = mock(OpenAiImageRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->once()->andReturn(base64_encode('あああああ'));
        $result = $repository->getImage();
        $this->assertEquals('あああああ', $result);
    }


}