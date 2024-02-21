<?php

namespace Tests\Feature;

use App\Services\AuthorService;
use App\Models\Author;
use App\Models\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorServiceTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Author $model;
    private Authorservice $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(AuthorService::class);
    }

    /**
     * @test
     */
    public function addOrFind_追加_正常(): void
    {
        $this->assertDatabaseMissing('authors', ['name' => 'テスト著者']);
        $result = $this->service->addOrFind('テスト著者');
        $this->assertDatabaseHas('authors', ['name' => 'テスト著者']);
        $this->assertEquals('テスト著者', $result['name']);
    }

    /**
     * @test
     */
    public function addOrFind_追加_属性_正常(): void
    {
        $this->assertDatabaseMissing('authors', ['name' => 'テスト著者']);
        $attr1 = Attribute::factory()->create();
        $attr2 = Attribute::factory()->create();
        $result = $this->service->addOrFind('テスト著者',[
            $attr1->toArray(),
            $attr2->toArray()
        ]);
        $this->assertDatabaseHas('authors', ['name' => 'テスト著者']);
        $this->assertEquals('テスト著者', $result['name']);
        $this->assertDatabaseHas('attribute_author', ['author_id' => $result['id'], 'attribute_id' => $attr1->id]);
        $this->assertDatabaseHas('attribute_author', ['author_id' => $result['id'], 'attribute_id' => $attr2->id]);
    }

    /**
     * @test
     */
    public function addOrFind_既存_正常(): void
    {
        $author = Author::factory()->create();
        $this->assertDatabaseHas('authors', ['id' => $author->id]);
        $result = $this->service->addOrFind($author->name);
        $this->assertEquals($author->id, $result['id']);
    }

    /**
     * @test
     */
    public function addOrFind_既存_属性_無視(): void
    {
        $attr1 = Attribute::factory()->create();
        $author = Author::factory()->create();
        $author->attributes()->sync($attr1);
        $this->assertDatabaseHas('authors', ['id' => $author->id]);
        $this->assertDatabaseHas('attribute_author', ['author_id' => $author->id, 'attribute_id' => $attr1->id]);
        
        $attr2 = Attribute::factory()->create();
        $result = $this->service->addOrFind($author->name,[
            $attr2->toArray()
        ]);
        $this->assertEquals($author->id, $result['id']);
        $this->assertDatabaseHas('attribute_author', ['author_id' => $result['id'], 'attribute_id' => $attr1->id]);
        $this->assertDatabaseMissing('attribute_author', ['author_id' => $result['id'], 'attribute_id' => $attr2->id]);
    }


}
