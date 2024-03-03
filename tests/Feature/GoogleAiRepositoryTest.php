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
    public function setContent_正常(): void
    {
        $repository = new GoogleAiRepository();
        $repository->setContent('テスト');
        $content = $this->getPrivateProperty('content', $repository);
        $this->assertEquals([['text' => 'テスト']], $content['contents']['parts']);
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
                            'candidates' => [['content'=>['parts' => [['text' => 'レスポンス']]]]]
                        ];}
                    };
                }
            });
        $repository = new GoogleAiRepository();
        $repository->setContent('テスト');
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
