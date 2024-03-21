<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GalleryActionTest extends FeatureTestCase
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
        $items = [];
        for ($i=10; $i < 23; $i++) {
            $article = Article::factory()->create([
                'author_id' => $this->author->id, 
                'locale' => app()->currentLocale(),
                'created_at' => new \DateTime('2021-01-' . $i),
            ]);
            $item = Image::factory()->create([
                'article_id' => $article->id,
                'path' => public_path() . '/img/test' . $article->id . '.png',
                'model_name' => 'test',
                'created_at' => new \DateTime('2021-01-' . $i),
            ]);
            $items[] = ['id' => $item->id];
        }
        $ids = array_reverse($items);
        $missing = array_pop($ids);
        $this->post('/gallery')->assertStatus(200)
            ->assertJson(['data' => $ids])
            ->assertJsonMissing(['data' => $missing])
            ->assertJson(['_links' => ['next' => ['href' => '/gallery?page=2']]]);
    }

}
