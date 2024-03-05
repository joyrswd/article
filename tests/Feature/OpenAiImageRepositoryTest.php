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
    public function addPrompt_正常(): void
    {
        $this->repository->addPrompt('テスト');
        $prompt = $this->getPrivateProperty('prompt', $this->repository);
        $this->assertContains('テスト', $prompt);
    }

    /**
     * @test
     */
    public function prepareContent_正常(): void
    {
        $repository = new OpenAiImageRepository();
        $repository->addPrompt('テスト');
        $content = $this->callPrivateMethod('prepareContent', $repository);
        $model = $this->getPrivateProperty('model', $repository);
        $this->assertEquals([
            'model' => $model,
            'n' => 1,
            'quality' => 'standard',
            'response_format' => 'b64_json',
            'size' => '1024x1024',
            'prompt' => 'テスト',
        ], $content);
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
        $this->repository->addPrompt('画像生成');
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
