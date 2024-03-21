<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleActionTest extends FeatureTestCase
{
    use RefreshDatabase;

    private Author $author;

    public function setUp() :void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode('32charactersforLaravelTestAppKey')]);
        $this->author = Author::factory()->create();
    }

    /**
     * @test
     */
    public function 正常()
    {
        $item = Article::factory()->create(['author_id' => $this->author->id, 'locale' => app()->currentLocale()]);
        $this->post('/post/' . $item->id)->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'llm_name' => $item->llm_name,
                'date' => $item->created_at->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * @test
     */
    public function notfound()
    {
        Article::factory()->create(['author_id' => $this->author->id, 'locale' => app()->currentLocale()]);
        $this->post('/post/9999')->assertStatus(404);
    }

    /**
     * @test
     */
    public function locale_notfound()
    {
        $locale = app()->currentLocale();
        $article = Article::factory()->create(['author_id' => $this->author->id, 'locale' => $locale . 'dummy']);
        $this->post('/post/' . $article->id)->assertStatus(404);
    }
}
