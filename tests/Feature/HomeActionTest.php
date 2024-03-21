<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeActionTest extends FeatureTestCase
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
            $item = Article::factory()->create([
                'author_id' => $this->author->id, 
                'locale' => app()->currentLocale(),
                'created_at' => new \DateTime('2021-01-' . $i),
            ]);
            $items[] = ['id' => $item->id];
        }
        $ids = array_reverse($items);
        $missing = array_pop($ids);
        $this->post('/home')->assertStatus(200)
            ->assertJson(['data' => $ids])
            ->assertJsonMissing(['data' => $missing]);
    }

}
