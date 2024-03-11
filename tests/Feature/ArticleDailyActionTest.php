<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleDailyActionTest extends FeatureTestCase
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
        $item1 = Article::factory()->create([
            'author_id' => $this->author->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-01'),
        ]);
        $item2 = Article::factory()->create([
            'author_id' => $this->author->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-01'),
        ]);
        $item3 = Article::factory()->create([
            'author_id' => $this->author->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-02'),
        ]);
        $this->post('/date/2021-01-01')->assertStatus(200)
        ->assertJson([
            'data' => [
                ['id' => $item1->id,],
                ['id' => $item2->id,],
            ],
        ])->assertJsonMissing([
            'data' => [
                ['id' => $item3->id,],
            ],
        ]);
    }

    /**
     * @test
     */
    public function notfound()
    {
        Article::factory()->create([
            'author_id' => $this->author->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-01'),
        ]);
        $this->post('/date/2021-01-02')->assertStatus(404);
    }

    /**
     * @test
     */
    public function locale_notfound()
    {
        $locale = app()->currentLocale();
        Article::factory()->create([
            'author_id' => $this->author->id, 
            'locale' => $locale . 'dummy',
            'created_at' => new \DateTime('2021-01-01'),
        ]);
        $this->post('/date/2021-01-01')->assertStatus(404);
    }

}
