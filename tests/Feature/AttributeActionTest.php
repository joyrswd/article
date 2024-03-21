<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttributeActionTest extends FeatureTestCase
{
    use RefreshDatabase;

    private Author $author;
    private Attribute $attribute;

    public function setUp() :void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode('32charactersforLaravelTestAppKey')]);
        $this->author = Author::factory()->create();
        $this->attribute = Attribute::factory()->create();
        $this->author->attributes()->sync($this->attribute);
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
        $author2 = Author::factory()->create();
        $author2->attributes()->sync($this->attribute);
        $item2 = Article::factory()->create([
            'author_id' => $author2->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-01'),
        ]);
        $author3 = Author::factory()->create();
        $item3 = Article::factory()->create([
            'author_id' => $author3->id, 
            'locale' => app()->currentLocale(),
            'created_at' => new \DateTime('2021-01-02'),
        ]);
        $this->post('/attr/' . $this->attribute->id)->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $this->attribute->id,
                "_embedded" => ['posts' => [
                    ['id' => $item1->id,],
                    ['id' => $item2->id,],
                ]],
            ],
        ])->assertJsonMissing([
            'data' => [
                'id' => $this->attribute->id,
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
        $this->post('/attr/' . $this->attribute->id+1 )->assertStatus(404);
    }

}
