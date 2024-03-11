<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorActionTest extends FeatureTestCase
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
        $author = Author::factory()->create();
        $item3 = Article::factory()->create([
            'author_id' => $author->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-02'),
        ]);
        $this->post('/user/' . $this->author->id)->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $this->author->id,
                "_embedded" => ['posts' => [
                    ['id' => $item1->id,],
                    ['id' => $item2->id,],
                ]],
            ],
        ])->assertJsonMissing([
            'data' => [
                'id' => $this->author->id,
                "_embedded" => ['posts' => [
                    ['id' => $item3->id,],
                ]],
            ]
        ]);
    }

    /**
     * @test
     */
    public function notfound()
    {
        $this->post('/user/' . $this->author->id+1 )->assertStatus(404);
    }

}
