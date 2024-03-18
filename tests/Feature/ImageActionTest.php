<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageActionTest extends FeatureTestCase
{
    use RefreshDatabase;

    private Author $author;
    private Article $article;

    public function setUp() :void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode('32charactersforLaravelTestAppKey')]);
        $this->author = Author::factory()->create();
        $this->article = Article::factory()->create(['author_id' => $this->author->id, 'locale' => app()->currentLocale()]);
    }

    /**
     * @test
     */
    public function 正常()
    {
        $item = Image::factory()->create(['article_id' => $this->article->id]);
        $this->post('/image/' . $item->id)->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $item->id,
                '_embedded' => [
                    'post' => [
                        'id' => $this->article->id,
                    ]
                ]
            ],
        ]);
    }

    /**
     * @test
     */
    public function notfound()
    {
        $this->post('/image/9999')->assertStatus(404);
    }

    /**
     * @test
     */
    public function locale_notfound()
    {
        $locale = app()->currentLocale();
        $article = Article::factory()->create(['author_id' => $this->author->id, 'locale' => $locale . 'dummy']);
        $item = Image::factory()->create(['article_id' => $article->id]);
        $this->post('/image/' . $item->id)->assertStatus(404);
    }
}
