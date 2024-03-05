<?php

namespace Tests\Feature;

use App\Repositories\ClaudeRepository;
use Illuminate\Support\Facades\Http;

class ClaudeRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function addPrompt_正常(): void
    {
        $repository = new ClaudeRepository();
        $repository->addPrompt('テスト');
        $prompt = $this->getPrivateProperty('prompt', $repository);
        $this->assertContains('テスト', $prompt);
    }

    /**
     * @test
     */
    public function prepareContent_正常(): void
    {
        $repository = new ClaudeRepository();
        $repository->addPrompt('テスト1');
        $repository->addPrompt('テスト2');
        $content = $this->callPrivateMethod('prepareContent', $repository);
        $model = $this->getPrivateProperty('model', $repository);
        $this->assertEquals([
            'model' => $model,
            'max_tokens' => 1000,
            'messages' => [['role' => 'user', 'content' => "テスト1\nテスト2"]],
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
                public function post () {
                    return new class {
                        public function json() {return [
                            'content' => [['text'=>'レスポンス']]
                        ];}
                    };
                }
            });
        $repository = new ClaudeRepository();
        $repository->addPrompt('テスト');
        $result = $repository->requestApi();
        $this->assertEquals('レスポンス', $result);
    }

    /**
     * @test
     */
    public function getModel_正常(): void
    {
        $repository = new ClaudeRepository();
        $this->setPrivateProperty('model', 'test', $repository);
        $result = $repository->getModel();
        $this->assertEquals('test', $result);
    }

}
