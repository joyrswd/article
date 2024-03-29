<?php

namespace Tests\Feature;

use App\Repositories\WikipediaRepository;
use Illuminate\Support\Facades\Http;

class WikipediaRepositoryTest extends FeatureTestCase
{

    private WikipediaRepository $repository;

    public function setUp():void
    {
        parent::setUp();
        $this->repository = new WikipediaRepository();
    }

    /**
     * @test
     */
    public function addPrompt_正常(): void
    {
        $this->repository->addPrompt(date('2月2日'));
        $prompt = $this->getPrivateProperty('prompt', $this->repository);
        $this->assertContains('2月2日', $prompt);
    }

    /**
     * @test
     */
    public function prepareContent_正常(): void
    {
        $this->repository->addPrompt('テスト');
        $content = $this->callPrivateMethod('prepareContent', $this->repository);
        $this->assertEquals([
            'format' => 'json',
            'action' => 'query',
            'prop' => 'extracts',
            'explaintext' => 1,
            'redirects' => 1,
            'exlimit' => 1,
            'formatversion' => 2,
            'titles' => 'テスト',
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
                public function get () {
                    return new class {
                        public function throw() {}
                        public function json() {
                            return ['query' => ['pages' => [ 100=> ['extract'=>"リード文\n== カテゴリー ==\nレスポンス"]]]];
                        }
                    };
                }
            });
        $this->repository->addPrompt('日付');
        $result = $this->repository->requestApi();
        $this->assertEquals(['リード文','カテゴリー' => ['レスポンス']], $result);
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
