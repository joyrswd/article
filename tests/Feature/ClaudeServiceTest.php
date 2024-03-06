<?php

namespace Tests\Feature;

use App\Services\ClaudeService;
use App\Repositories\ClaudeRepository;
use App\Repositories\StableDiffusionRepository;
use App\Repositories\DeepLRepository;
use App\Repositories\WikipediaRepository;
use App\Services\ImageService;
use DateTime;
use Mockery;

class ClaudeServiceTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function makeAuthor_正常(): void
    {
        $result = $this->callPrivateMethod('makeAuthor', app(ClaudeService::class));
        $this->assertStringMatchesFormat('%s大好きの%sで%sな%s', $result);
    }

    /**
     * @test
     */
    public function convert_正常(): void
    {
        $text = '{author}です。';
        $author = '作者';
        $result = $this->callPrivateMethod('convert', app(ClaudeService::class), $text, $author);
        $this->assertEquals('作者です。', $result);
    }

    /**
     * @test
     */
    public function makeCommand_正常(): void
    {
        $date = new DateTime('2021-02-01');
        $author = '元気な作者';
        $service = app(ClaudeService::class);
        $result = $this->callPrivateMethod('makeCommand', $service, $author, $date);
        $message = <<<MESSAGE
あなたは『日本語』を母語とする『元気な作者』です。
次に『2月1日』に関する情報を示すので、あなたの興味ある情報を選び『日本語』で記事を書いてください。
MESSAGE;
        $this->assertEquals($message, $result);
    }

    /**
     * @test
     */
    public function makeConditons_正常(): void
    {
        $author = '元気な作者';
        $service = app(ClaudeService::class);
        $this->setPrivateProperty('conditions', [], $service);
        $result = $this->callPrivateMethod('makeConditons', $service, $author);
        $message = <<<MESSAGE
記事の作成は次のルールに従ってください。
- 『{$author}』が書くような文体にしてください。
- 記事にはあなたの考えや感想、体験などを含めてください。
MESSAGE;
        $this->assertEquals($message, $result);
    }

    /**
     * @test
     */
    public function makeReference_正常(): void
    {
        $date = new DateTime('2021-02-01');
        $service = app(ClaudeService::class);
        $this->setPrivateProperty('conditions', [], $service);
        $repository = mock(WikipediaRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->once()->andReturn('reference');
        $this->app->instance(WikipediaRepository::class, $repository);
        $result = $this->callPrivateMethod('makeReference', $service, $date);
        $this->assertEquals('reference', $result);
    }

    /**
     * @test
     */
    public function makeArticle_正常(): void
    {
        $wiki = Mockery::mock(ClaudeRepository::class)->makePartial();
        $wiki->shouldReceive('requestApi')->andReturn('reference');
        $this->app->instance(WikipediaRepository::class, $wiki);
        $repository = Mockery::mock(ClaudeRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->andReturn('articleテスト');
        $result = $this->callPrivateMethod('makeArticle', new ClaudeService($repository, app(StableDiffusionRepository::class)), '著者', new DateTime('2021-05-01'));
        $this->assertEquals('articleテスト', $result);
    } 

    /**
     * @test
     */
    public function makeTitle_正常(): void
    {
        $repository = Mockery::mock(ClaudeRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->andReturn('titleテスト');
        $result = $this->callPrivateMethod('makeTitle', new ClaudeService($repository, app(StableDiffusionRepository::class)), '文章');
        $this->assertEquals('titleテスト', $result);
    }    
    
    /**
     * @test
     */
    public function makePost_正常(): void
    {
        $repository = Mockery::mock(ClaudeRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->andReturn('テスト');
        $repository->shouldReceive('getModel')->andReturn('test');
        $class = new ClaudeService($repository, app(StableDiffusionRepository::class));
        $date = new \DateTime();
        $result = $class->makePost($date);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('article', $result);
        $this->assertArrayHasKey('author', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('model', $result);
        $this->assertEquals('テスト', $result['title']);
        $this->assertEquals('テスト', $result['article']);
    }

    /**
     * @test
     */
    public function getlang_正常() :void
    {
        $result = $this->callPrivateMethod('getLang', app(ClaudeService::class));
        $this->assertEquals('日本語', $result);
    }

    /**
     * @test
     */
    public function getlang_英語_正常() :void
    {
        app()->setLocale('en');
        $result = $this->callPrivateMethod('getLang', app(ClaudeService::class));
        $this->assertEquals('英語', $result);
    }
    
    /**
     * @test
     */
    public function validateLang_正常() :void
    {
        $result = $this->callPrivateMethod('validateLang', app(ClaudeService::class), 'ああああ');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function validateLang_英語_正常() :void
    {
        app()->setLocale('en');
        $result = $this->callPrivateMethod('validateLang', app(ClaudeService::class), 'aaaa');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function validateLang_英語_異常() :void
    {
        app()->setLocale('en');
        $result = $this->callPrivateMethod('validateLang', app(ClaudeService::class), 'aaあaa');
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function transrateArticle_正常(): void
    {
        $translater = Mockery::mock(DeepLRepository::class)->makePartial();
        $translater->shouldReceive('requestApi')->andReturn('transrated');
        $this->app->instance(DeepLRepository::class, $translater);
        $result = $this->callPrivateMethod('translateArticle', app(ClaudeService::class), '文章');
        $this->assertEquals('transrated', $result);
    }

}
