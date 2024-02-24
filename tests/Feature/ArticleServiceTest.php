<?php

namespace Tests\Feature;

use App\Services\ArticleService;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleServiceTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Author $model;
    private Articleservice $service;
    private Author $author;

    public function setUp(): void
    {
        parent::setUp();
        $this->author = Author::factory()->create();
        $this->service = app(ArticleService::class);
    }

    /**
     * @test
     */
    public function add_正常(): void
    {
        $locale = app()->currentLocale();
        $this->assertDatabaseMissing('articles', ['title' => 'テストタイトル']);
        $result = $this->service->add($this->author->id, 'テストタイトル', 'テスト本文', 'ai');
        $this->assertDatabaseHas('articles', [
            'author_id' => $this->author->id,
            'title' => 'テストタイトル',
            'content' => 'テスト本文',
            'llm_name' => 'ai',
            'locale' => $locale
        ]);
        $this->assertEquals('テストタイトル', $result['title']);
        $this->assertEquals('テスト本文', $result['content']);
        $this->assertEquals('ai', $result['llm_name']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($this->author->name, $result['author']['name']);
        $this->assertNotEmpty($this->author->articles()->findOrNew($result['id']));
    }

    /**
     * @test
     */
    public function add_ロケール変更_正常(): void
    {
        app()->setLocale('en');
        $this->assertDatabaseMissing('articles', ['title' => 'テストタイトル']);
        $result = $this->service->add($this->author->id, 'テストタイトル', 'テスト本文', 'ai');
        $this->assertDatabaseHas('articles', [
            'author_id' => $this->author->id,
            'title' => 'テストタイトル',
            'content' => 'テスト本文',
            'llm_name' => 'ai',
            'locale' => 'en'
        ]);
        $this->assertEquals('テストタイトル', $result['title']);
        $this->assertEquals('テスト本文', $result['content']);
        $this->assertEquals('ai', $result['llm_name']);
        $this->assertEquals('en', $result['locale']);
        $this->assertEquals($this->author->name, $result['author']['name']);
        $this->assertNotEmpty($this->author->articles()->findOrNew($result['id']));
    }

}
