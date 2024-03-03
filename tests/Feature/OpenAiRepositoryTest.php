<?php

namespace Tests\Feature;

use App\Repositories\OpenAiRepository;
use Illuminate\Support\Facades\Http;

class OpenAiRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function setContent_正常(): void
    {
        $repository = new OpenAiRepository();
        $repository->setContent('テスト');
        $content = $this->getPrivateProperty('content', $repository);
        $this->assertEquals([['role' => 'user', 'content' => 'テスト']], $content['messages']);
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
                            'choices' => [['message'=>['content' => 'レスポンス']]]
                        ];}
                    };
                }
            });
        $repository = new OpenAiRepository();
        $repository->setContent('テスト');
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
