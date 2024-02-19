<?php

namespace Tests\Feature;

use App\Services\OpenAiService;
use App\Repositories\OpenAiRepository;
use DateTime;
use Mockery;

class OpenAiServiceTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function makeAuthor_正常(): void
    {
        $result = $this->callPrivateMethod('makeAuthor', app(OpenAiService::class));
        $this->assertStringMatchesFormat('%sマニアの%sで%sな%s', $result);
    }

    /**
     * @test
     */
    public function convert_正常(): void
    {
        $text = '{month}の{author}。';
        $month = '1月';
        $author = '作者';
        $result = $this->callPrivateMethod('convert', app(OpenAiService::class), $text, $author, $month);
        $this->assertEquals('1月の作者。', $result);
    }

    /**
     * @test
     */
    public function makeSystemMessage_正常(): void
    {
        $date = new DateTime('2021-02-01');
        $author = '元気な作者';
        $service = app(OpenAiService::class);
        $this->setPrivateProperty('conditions', [], $service);
        $result = $this->callPrivateMethod('makeSystemMessage', $service, $author, $date);
        $message = <<<MESSAGE
あなたは元気な作者です。
2月にまつわる記事を書いてください。
元気な作者が書くような内容と文体にしてください。
MESSAGE;
        $this->assertEquals($message, $result);
    }

    /**
     * @test
     */
    public function makeArticle_正常(): void
    {
        $repository = Mockery::mock(OpenAiRepository::class);
        $repository->shouldReceive('setMessage');
        $repository->shouldReceive('excute')
            ->andReturn([
                'choices' => [
                    ['message' => [
                        'content' => 'articleテスト'
                    ]]
                ]
            ]);
        $class = new OpenAiService($repository);
        $date = new \DateTime();
        $result = $class->makeArticle('著者', new DateTime('2021-05-01'));
        $this->assertEquals('articleテスト', $result);
    } 
    
    /**
     * @test
     */
    public function makeTitle_正常(): void
    {
        $repository = Mockery::mock(OpenAiRepository::class);
        $repository->shouldReceive('setMessage');
        $repository->shouldReceive('excute')
            ->andReturn([
                'choices' => [
                    ['message' => [
                        'content' => 'titleテスト'
                    ]]
                ]
            ]);
        $class = new OpenAiService($repository);
        $result = $class->makeTitle('文章');
        $this->assertEquals('titleテスト', $result);
    }    
    
    /**
     * @test
     */
    public function makePost_正常(): void
    {
        $repository = Mockery::mock(OpenAiRepository::class);
        $repository->shouldReceive('setMessage');
        $repository->shouldReceive('excute')
            ->andReturn([
                'choices' => [
                    ['message' => [
                        'content' => 'テスト'
                    ]]
                ]
            ]);
        $class = new OpenAiService($repository);
        $date = new \DateTime();
        $result = $class->makePost($date);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('article', $result);
        $this->assertArrayHasKey('author', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertEquals('テスト', $result['title']);
        $this->assertEquals('テスト', $result['article']);
    }
}
