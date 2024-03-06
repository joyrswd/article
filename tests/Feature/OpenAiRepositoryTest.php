<?php

namespace Tests\Feature;

use App\Repositories\OpenAiRepository;
use Illuminate\Support\Facades\Http;

class OpenAiRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function addPrompt_正常(): void
    {
        $repository = new OpenAiRepository();
        $repository->addPrompt('テスト');
        $prompt = $this->getPrivateProperty('prompt', $repository);
        $this->assertContains('テスト', $prompt);
    }

    /**
     * @test
     */
    public function prepareContent_正常(): void
    {
        $repository = new OpenAiRepository();
        $repository->addPrompt('テスト');
        $content = $this->callPrivateMethod('prepareContent', $repository);
        $model = $this->getPrivateProperty('model', $repository);
        $this->assertEquals([
            'model' => $model,
            'presence_penalty' => 1,
            'top_p' => 0,
            'messages' => [["role" => 'user', "content" => 'テスト']],
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
                        public function throw() {}
                        public function json() {
                            return ['choices' => [['message'=>['content' => 'レスポンス']]]];
                        }
                    };
                }
            });
        $repository = new OpenAiRepository();
        $repository->addPrompt('テスト');
        $result = $repository->requestApi();
        $this->assertEquals('レスポンス', $result);
    }

    /**
     * @test
     */
    public function getModel_正常(): void
    {
        $repository = new OpenAiRepository();
        $this->setPrivateProperty('model', 'test', $repository);
        $result = $repository->getModel();
        $this->assertEquals('test', $result);
    }

}
