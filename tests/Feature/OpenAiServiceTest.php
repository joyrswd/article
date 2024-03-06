<?php

namespace Tests\Feature;

use App\Services\OpenAiService;
use App\Repositories\OpenAiRepository;
use App\Repositories\OpenAiImageRepository;
use App\Repositories\DeepLRepository;
use App\Repositories\WikipediaRepository;
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
        $this->assertStringMatchesFormat('%s大好きの%sで%sな%s', $result);
    }

    /**
     * @test
     */
    public function convert_正常(): void
    {
        $text = '{author}です。';
        $author = '作者';
        $result = $this->callPrivateMethod('convert', app(OpenAiService::class), $text, $author);
        $this->assertEquals('作者です。', $result);
    }

    /**
     * @test
     */
    public function makeCommand_正常(): void
    {
        $date = new DateTime('2021-02-01');
        $author = '元気な作者';
        $service = app(OpenAiService::class);
        $result = $this->callPrivateMethod('makeCommand', $service, $author, $date);
        $message = <<<MESSAGE
あなたは『日本語』を母語とする『元気な作者』です。
次に『2月1日』に関する情報を示すので、あなたの興味ある情報を選び記事を書いてください。
MESSAGE;
        $this->assertEquals($message, $result);
    }

    /**
     * @test
     */
    public function makeConditons_正常(): void
    {
        $author = '元気な作者';
        $service = app(OpenAiService::class);
        $lang = $this->callPrivateMethod('getLang', $service);
        $this->setPrivateProperty('conditions', [], $service);
        $result = $this->callPrivateMethod('makeConditons', $service, $author);
        $message = <<<MESSAGE
記事の作成は次のルールに従ってください。
- 『{$lang}』で書いてください。
- 『{$author}』が書くような文体にしてください。
- 箇条書きにせず、エッセイのようにしてください。
MESSAGE;
        $this->assertEquals($message, $result);
    }

    /**
     * @test
     */
    public function makeReference_正常(): void
    {
        $date = new DateTime('2021-02-01');
        $service = app(OpenAiService::class);
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
        $wiki = Mockery::mock(OpenAiRepository::class)->makePartial();
        $wiki->shouldReceive('requestApi')->andReturn('reference');
        $this->app->instance(WikipediaRepository::class, $wiki);
        $repository = Mockery::mock(OpenAiRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->andReturn('articleテスト');
        $result = $this->callPrivateMethod('makeArticle', new OpenAiService($repository, app(OpenAiImageRepository::class)), '著者', new DateTime('2021-05-01'));
        $this->assertEquals('articleテスト', $result);
    } 

    /**
     * @test
     */
    public function makeTitle_正常(): void
    {
        $repository = Mockery::mock(OpenAiRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->andReturn('titleテスト');
        $result = $this->callPrivateMethod('makeTitle', new OpenAiService($repository, app(OpenAiImageRepository::class)), '文章');
        $this->assertEquals('titleテスト', $result);
    }
    
    /**
     * @test
     */
    public function makePost_正常(): void
    {
        $repository = Mockery::mock(OpenAiRepository::class)->makePartial();
        $repository->shouldReceive('requestApi')->andReturn('テスト');
        $repository->shouldReceive('getModel')->andReturn('test');
        $service = new OpenAiService($repository, Mockery::mock(OpenAiImageRepository::class));
        $date = new \DateTime();
        $result = $service->makePost($date);
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
        $result = $this->callPrivateMethod('getLang', app(OpenAiService::class));
        $this->assertEquals('日本語', $result);
    }

    /**
     * @test
     */
    public function getlang_英語_正常() :void
    {
        app()->setLocale('en');
        $result = $this->callPrivateMethod('getLang', app(OpenAiService::class));
        $this->assertEquals('英語', $result);
    }

    /**
     * @test
     */
    public function validateLang_正常() :void
    {
        $result = $this->callPrivateMethod('validateLang', app(OpenAiService::class), 'ああああ');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function validateLang_英語_正常() :void
    {
        app()->setLocale('en');
        $result = $this->callPrivateMethod('validateLang', app(OpenAiService::class), 'aaaa');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function validateLang_英語_異常() :void
    {
        app()->setLocale('en');
        $result = $this->callPrivateMethod('validateLang', app(OpenAiService::class), 'aaあaa');
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function translateArticle_正常(): void
    {
        $translater = Mockery::mock(DeepLRepository::class)->makePartial();
        $translater->shouldReceive('requestApi')->andReturn('article');
        $this->app->instance(DeepLRepository::class, $translater);
        $result = $this->callPrivateMethod('translateArticle', app(OpenAiService::class), '文章');
        $this->assertEquals('article', $result);
    }

}
