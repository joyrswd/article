<?php

namespace Tests\Feature;

use App\Repositories\GoogleAiRepository;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Assert;

class GoogleAiRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function addPrompt_正常(): void
    {
        $repository = new GoogleAiRepository();
        $repository->addPrompt('テスト');
        $prompt = $this->getPrivateProperty('prompt', $repository);
        $this->assertContains('テスト', $prompt);
    }

    /**
     * @test
     */
    public function prepareContent_正常(): void
    {
        $repository = new GoogleAiRepository();
        $repository->addPrompt('テスト');
        $content = $this->callPrivateMethod('prepareContent', $repository);
        $this->assertEquals([
            'contents' => [
                'role' => 'user',
                'parts' => [['text' => 'テスト']]
            ]
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
                            return ['candidates' => [['content'=>['parts' => [['text' => 'レスポンス']]]]]];
                        }
                    };
                }
            });
        $repository = new GoogleAiRepository();
        $repository->addPrompt('テスト');
        $result = $repository->requestApi();
        $this->assertEquals('レスポンス', $result);
    }

    /**
     * @test
     */
    public function getModel_正常(): void
    {
        $repository = new GoogleAiRepository();
        $this->setPrivateProperty('model', 'test', $repository);
        $result = $repository->getModel();
        $this->assertEquals('test', $result);
    }
}
