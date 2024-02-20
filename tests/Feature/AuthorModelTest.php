<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorModelTest extends FeatureTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function 作成()
    {
        $model = Author::factory()->create();
        $this->assertInstanceOf(Author::class, $model);
        $this->assertDatabaseHas('authors', ['id' => $model->id]);
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }

    /**
     * @test
     */
    public function 更新()
    {
        $model = Author::factory()->create();
        $model->update([
            'name' => 'Updated Name',
        ]);
        $this->assertEquals('Updated Name', $model->name);
    }

    /**
     * @test
     */
    public function 削除()
    {
        $model = Author::factory()->create();
        $model->forceDelete();
        $this->assertModelMissing($model);
    }

        /**
     * @test
     */
    public function ソフトデリート()
    {
        $model = Author::factory()->create();
        $model->delete();
        $this->assertSoftDeleted($model);
    }

    /**
     * @test
     */
    public function リストア()
    {
        $model = Author::factory()->create();
        $model->delete();
        $model->restore();
        $this->assertDatabaseHas('authors', ['id' => $model->id]);
    }

    /**
     * @test
     */
    public function articles()
    {
        $model = Author::factory()->create();
        $children = $model->articles()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'locale' => 'ja',
            'llm_name' => 'ai',
        ]);
        $this->assertInstanceOf(Author::class, $children->author);
    }

    /**
     * @test
     */
    public function attributes()
    {
        $attribute1 = Attribute::factory()->create();
        $attribute2 = Attribute::factory()->create();
        $model = Author::factory()->create();
        $model->attributes()->attach([$attribute1->id, $attribute2->id]);
        $this->assertCount(2, $model->attributes);
        $this->assertInstanceOf(Author::class, $model->attributes->first()->authors->first());
    }

}
