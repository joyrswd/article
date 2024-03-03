<?php

namespace Tests\Feature;

use App\Repositories\DeepLRepository;
use Illuminate\Support\Facades\Http;

class DeepLRepositoryTest extends FeatureTestCase
{

    private DeepLRepository $repository;

    public function setUp():void
    {
        parent::setUp();
        $this->repository = new DeepLRepository();
    }

    /**
     * @test
     */
    public function setContent_正常(): void
    {
        $this->repository->setContent('テスト');
        $content = $this->getPrivateProperty('content', $this->repository);
        $this->assertEquals(['テスト'], $content['text']);
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
                            'translations' => [['text'=>'レスポンス']]
                        ];}
                    };
                }
            });
        $this->repository->setContent('翻訳テキスト');
        $result = $this->repository->requestApi();
        $this->assertEquals('レスポンス', $result);
    }

    /**
     * @test
     */
    public function setLang_正常(): void
    {
        $this->repository->setLang('テスト');
        $content = $this->getPrivateProperty('content', $this->repository);
        $this->assertEquals('テスト', $content['target_lang']);
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
