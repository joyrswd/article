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
    public function prepareContent_正常(): void
    {
        $this->repository->addPrompt('テスト');
        $content = $this->callPrivateMethod('prepareContent', $this->repository);
        $lang = $this->getPrivateProperty('lang', $this->repository);
        $this->assertEquals([
            'text' => ['テスト'],
            'target_lang' => strtoupper($lang)
        ], $content);
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
    public function requestApi_正常(): void
    {
        Http::fake();
        Http::shouldReceive('withHeaders')
            ->once()->andReturn(new class {
                public function timeout() {}
                public function withToken() {}
                public function post () {
                    return new class {
                        public function throw() {}
                        public function json() {
                            return ['translations' => [['text'=>'レスポンス']]];
                        }
                    };
                }
            });
        $this->repository->addPrompt('翻訳テキスト');
        $result = $this->repository->requestApi();
        $this->assertEquals('レスポンス', $result);
    }

    /**
     * @test
     */
    public function setLang_正常(): void
    {
        $this->repository->setLang('テスト');
        $lang = $this->getPrivateProperty('lang', $this->repository);
        $this->assertEquals('テスト', $lang);
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
