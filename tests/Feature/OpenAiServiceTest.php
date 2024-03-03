<?php

namespace Tests\Feature;

use App\Services\OpenAiService;
use App\Repositories\OpenAiRepository;
use App\Repositories\OpenAiImageRepository;
use App\Repositories\DeepLRepository;
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
あなたは『日本語』を母語とする『元気な作者』です。
『日本語』を母語とする『元気な作者』が書くような内容と文体で、『2月』に関する記事を『日本語』で書いてください。
MESSAGE;
        $this->assertEquals($message, $result);
    }

    /**
     * @test
     */
    public function makeArticle_正常(): void
    {
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
        $result = $this->callPrivateMethod('translateArticle', app(OpenAiService::class), '文章', $translater);
        $this->assertEquals('article', $result);
    }    
    

}
